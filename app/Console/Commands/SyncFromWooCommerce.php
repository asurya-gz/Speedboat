<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WooCommerceService;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Models\Schedule;
use App\Models\Speedboat;
use App\Models\Destination;
use App\Models\SeatBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncFromWooCommerce extends Command
{
    protected $signature = 'woocommerce:sync-from
                            {--limit=20 : Number of orders to fetch}'; // Opsi 'since' kita hapus

    protected $description = 'Sync orders from WooCommerce to local database (Optimized)';

    protected $woocommerce;

    public function __construct(WooCommerceService $woocommerce)
    {
        parent::__construct();
        $this->woocommerce = $woocommerce;
    }

    public function handle()
    {
        $this->info('🔄 Starting WooCommerce sync (Online → Offline)...');

        // 1. Cek koneksi
        if (!$this->woocommerce->checkConnection()) {
            $this->error('❌ Cannot connect to WooCommerce API. Check your internet connection.');
            return 1;
        }

        $this->info('✅ Connection to WooCommerce established');

        // --- OPTIMASI DIMULAI DISINI ---

        // 2. Dapatkan timestamp 'created_at' dari order terakhir yang berhasil disinkronisasi
        $lastSyncedTransaction = Transaction::whereNotNull('woocommerce_order_id')
            ->orderBy('created_at', 'desc')
            ->first();

        // 3. Tentukan filter 'after'.
        // Kita ambil 10 menit *sebelum* order terakhir, untuk keamanan (jaga-jaga ada delay)
        // Jika DB kosong, kita ambil 1 hari terakhir
        $syncSince = $lastSyncedTransaction
            ? Carbon::parse($lastSyncedTransaction->created_at)->subMinutes(10)->toIso8601String()
            : Carbon::now()->subDay()->toIso8601String();

        // 4. Siapkan parameter query
        $params = [
            'per_page' => $this->option('limit'),
            'orderby' => 'date',
            'order' => 'asc', // PENTING: Ubah ke 'asc' (ascending) agar diproses berurutan
            'after' => $syncSince // INI KUNCINYA: Hanya ambil order SETELAH tanggal ini
        ];

        // --- OPTIMASI SELESAI ---

        // 5. Ambil data order dari WooCommerce
        $this->info("📥 Fetching new orders created after: {$syncSince}");
        $response = $this->woocommerce->getOrders($params);

        if (!$response['success']) {
            $this->error('❌ Failed to fetch orders: ' . $response['error']);
            return 1;
        }

        $orders = $response['data'];
        $ordersCount = is_array($orders) ? count($orders) : $orders->count();
        $this->info("Found {$ordersCount} new orders to process");

        $synced = 0;
        $skipped = 0;
        $errors = 0;

        // 6. Loop dan proses order (LOGIKA INI SEMUA TETAP SAMA)
        foreach ($orders as $order) {
            try {
                // Skip jika sudah ada (sebagai pengaman tambahan)
                if (Transaction::where('woocommerce_order_id', $order['id'])->exists()) {
                    $this->line("⏭️  Order #{$order['id']} already exists locally, skipping...");
                    $skipped++;
                    continue;
                }

                $this->info("Processing order #{$order['id']}...");

                // Parse order data
                $parsedData = $this->woocommerce->parseOrderToTransaction($order);

                // Find or create matching schedule
                $schedule = $this->findOrCreateSchedule($parsedData);

                if (!$schedule) {
                    $this->warn("⚠️  Could not match schedule for order #{$order['id']}, skipping...");
                    $skipped++;
                    continue;
                }

                // Create transaction and tickets
                DB::beginTransaction();
                try {
                    $transaction = $this->createTransaction($parsedData, $schedule, $order['id']);
                    $this->createTickets($parsedData, $transaction, $schedule, $order);
                    $this->createSeatBookings($parsedData, $transaction, $schedule);

                    DB::commit();

                    $this->info("✅ Successfully synced order #{$order['id']} → Transaction #{$transaction->transaction_code}");
                    $synced++;

                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }

            } catch (\Exception $e) {
                $this->error("❌ Error processing order #{$order['id']}: " . $e->getMessage());
                Log::error('Sync from WooCommerce failed', [
                    'order_id' => $order['id'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errors++;
            }
        }

        $this->newLine();
        $this->info("📊 Sync Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Synced', $synced],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );

        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | FUNGSI HELPER (TIDAK BERUBAH)
    |--------------------------------------------------------------------------
    */

    protected function findOrCreateSchedule($parsedData)
    {
        // Periksa apakah 'speedboat_name' ada di data.
        if (!isset($parsedData['speedboat_name']) || empty($parsedData['speedboat_name'])) {
            $this->warn("⚠️  'speedboat_name' key is missing from parsed data. Skipping this order.");
            return null; // Lewati order ini
        }
        // --- PERBAIKAN SELESAI ---

        // Find speedboat by name or WooCommerce bus ID
        $speedboat = Speedboat::where('name', $parsedData['speedboat_name'])
            ->orWhere('woocommerce_bus_id', $parsedData['woocommerce_bus_id'])
            ->first();

        if (!$speedboat) {
            $this->warn("⚠️  Speedboat '{$parsedData['speedboat_name']}' not found in local database");
            return null;
        }

        // Find destination
        $destination = Destination::where('departure_location', 'like', "%{$parsedData['departure_location']}%")
            ->where('destination_location', 'like', "%{$parsedData['destination_location']}%")
            ->first();

        if (!$destination) {
            $this->warn("⚠️  Destination {$parsedData['departure_location']} → {$parsedData['destination_location']} not found");
            return null;
        }

        // Find schedule by speedboat, destination, and time
        $schedule = Schedule::where('speedboat_id', $speedboat->id)
            ->where('destination_id', $destination->id)
            ->whereTime('departure_time', $parsedData['departure_time'])
            ->first();

        if (!$schedule) {
            // Create a new schedule if not found (optional - you can disable this if you don't want auto-creation)
            $this->warn("⚠️  Schedule not found for {$speedboat->name} at {$parsedData['departure_time']}, creating...");

            $schedule = Schedule::create([
                'destination_id' => $destination->id,
                'speedboat_id' => $speedboat->id,
                'name' => $speedboat->name . ' - ' . Carbon::parse($parsedData['departure_time'])->format('H:i'),
                'departure_time' => $parsedData['departure_time'],
                'capacity' => $speedboat->capacity,
                'is_active' => true
            ]);
        }

        return $schedule;
    }

    protected function createTransaction($parsedData, $schedule, $woocommerceOrderId)
    {
        return Transaction::create([
            'woocommerce_order_id' => $woocommerceOrderId,
            'transaction_code' => 'WC-' . $woocommerceOrderId . '-' . strtoupper(substr(md5($woocommerceOrderId), 0, 6)),
            'schedule_id' => $schedule->id,
            'departure_date' => $parsedData['departure_date'],
            'passenger_name' => $parsedData['passenger_name'],
            'adult_count' => $parsedData['adult_count'],
            'child_count' => $parsedData['child_count'],
            'toddler_count' => $parsedData['toddler_count'],
            'total_amount' => $parsedData['total_amount'],
            'payment_method' => $parsedData['payment_method'],
            'payment_status' => $parsedData['payment_status'],
            'paid_at' => $parsedData['paid_at'],
            'created_by' => 1, // System user
            'is_synced' => true,
            'synced_at' => now(),
            'created_at' => $parsedData['created_at'],
            'notes' => 'Synced from WooCommerce'
        ]);
    }

    protected function createTickets($parsedData, $transaction, $schedule, $order)
    {
        $seatMap = $parsedData['seat_passenger_map'] ?? [];
        $ticketCount = $parsedData['adult_count'] + $parsedData['toddler_count'];

        for ($i = 0; $i < $ticketCount; $i++) {
            $passengerName = $seatMap[$i + 1] ?? $parsedData['passenger_name'];
            $seatNumber = array_keys($seatMap)[$i] ?? null;

            $ticketCode = "WC-{$order['id']}-" . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            $qrData = json_encode([
                'ticket_code' => $ticketCode,
                'transaction_id' => $transaction->id,
                'woocommerce_order_id' => $order['id'],
                'schedule_id' => $schedule->id,
                'passenger_type' => $parsedData['passenger_type'],
                'seat_number' => $seatNumber,
                'destination' => $schedule->destination->departure_location . ' → ' . $schedule->destination->destination_location
            ]);

            Ticket::create([
                'woocommerce_line_item_id' => $parsedData['line_item_id'],
                'ticket_code' => $ticketCode,
                'transaction_id' => $transaction->id,
                'passenger_name' => $passengerName,
                'passenger_type' => $parsedData['passenger_type'],
                'price' => $parsedData['ticket_price'],
                'qr_code' => $qrData,
                'status' => 'active',
                'seat_number' => $seatNumber,
                'is_synced' => true,
                'synced_at' => now()
            ]);
        }
    }

    protected function createSeatBookings($parsedData, $transaction, $schedule)
    {
        $seatMap = $parsedData['seat_passenger_map'] ?? [];

        foreach ($seatMap as $seatIndex => $passengerName) {
            SeatBooking::create([
                'schedule_id' => $schedule->id,
                'departure_date' => $parsedData['departure_date'],
                'seat_number' => $seatIndex,
                'transaction_id' => $transaction->id,
                'passenger_name' => $passengerName,
                'passenger_type' => $parsedData['passenger_type'],
                'status' => 'booked'
            ]);
        }
    }
}