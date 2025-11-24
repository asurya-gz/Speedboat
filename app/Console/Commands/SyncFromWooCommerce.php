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
use App\Models\SyncLog; // Dipertahankan dari kode baru
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncFromWooCommerce extends Command
{
    protected $signature = 'woocommerce:sync-from
                            {--limit=20 : Number of orders to fetch}';

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

        // First, sync master data (speedboats, destinations, schedules)
        $this->newLine();
        $this->info('📦 Step 1: Syncing master data...');
        $this->call('woocommerce:sync-master-data'); // Dipertahankan dari kode baru
        $this->newLine();
        $this->info('📋 Step 2: Syncing transactions...');

        // --- OPTIMASI DIMULAI DISINI ---
        // (Ini adalah logika yang kita tambahkan kembali)

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

        // --- OPTIMASI SELESAI ---

        // 4. Siapkan parameter query
        $params = [
            'per_page' => $this->option('limit'),
            'orderby' => 'date',
            'order' => 'asc', // PENTING: 'asc' (ascending) agar diproses berurutan
            'after' => $syncSince // INI KUNCINYA: Hanya ambil order SETELAH tanggal ini
        ];

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

        // 6. Loop dan proses order
        foreach ($orders as $order) {
            $startTime = microtime(true);
            $logData = [ // Dipertahankan dari kode baru (SyncLog)
                'sync_type' => 'sync_from',
                'entity_type' => 'transaction',
                'trigger_source' => 'auto',
                'woocommerce_id' => $order['id'],
            ];

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
                    // Logika 'safety check' ini sudah ada di file baru Anda, jadi kita pertahankan
                    $this->warn("⚠️  Could not match schedule for order #{$order['id']}, skipping...");
                    $skipped++;
                    continue;
                }

                // Create transaction and tickets
                DB::beginTransaction();
                try {
                    $transaction = $this->createTransaction($parsedData, $schedule, $order['id']);
                    $this->createTickets($parsedData, $transaction, $schedule, $order);

                    // Memanggil fungsi createSeatBookings yang baru (lebih canggih)
                    $this->createSeatBookings($parsedData, $transaction, $schedule);

                    DB::commit();

                    // Log success (Dipertahankan dari kode baru)
                    SyncLog::createLog(array_merge($logData, [
                        'status' => 'success',
                        'entity_id' => $transaction->id,
                        'duration_seconds' => microtime(true) - $startTime,
                    ]));

                    $this->info("✅ Successfully synced order #{$order['id']} → Transaction #{$transaction->transaction_code}");
                    $synced++;

                } catch (\Exception $e) {
                    DB::rollback();
                    throw $e;
                }

            } catch (\Exception $e) {
                $this->error("❌ Error processing order #{$order['id']}: " . $e->getMessage());

                // Log failure (Dipertahankan dari kode baru)
                SyncLog::createLog(array_merge($logData, [
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'duration_seconds' => microtime(true) - $startTime,
                ]));

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
    | FUNGSI HELPER
    |--------------------------------------------------------------------------
    |
    | Fungsi di bawah ini dipertahankan dari kode baru yang Anda berikan
    | (termasuk 'safety check' dan logika 'createSeatBookings' yang baru)
    |
    */

    protected function findOrCreateSchedule($parsedData)
    {
        // Periksa apakah 'speedboat_name' ada di data. (Ini adalah 'safety check' kita)
        if (!isset($parsedData['speedboat_name']) || empty($parsedData['speedboat_name'])) {
            $this->warn("⚠️  'speedboat_name' key is missing from parsed data. Skipping this order.");
            return null; // Lewati order ini
        }

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
        $seatNumbers = $parsedData['seat_numbers'] ?? [];  // NEW: Get seat numbers from WooCommerce
        $adultCount = $parsedData['adult_count'];
        $toddlerCount = $parsedData['toddler_count'];
        $ticketCount = $adultCount + $toddlerCount;
        $ticketIndex = 0;

        // Create adult tickets first
        for ($i = 0; $i < $adultCount; $i++) {
            $ticketIndex++;
            $passengerName = $seatMap[$ticketIndex] ?? $parsedData['passenger_name'];
            $seatNumber = $seatNumbers[$ticketIndex] ?? null;  // NEW: Get seat from WooCommerce

            $ticketCode = "WC-{$order['id']}-" . str_pad($ticketIndex, 3, '0', STR_PAD_LEFT);

            $qrData = json_encode([
                'ticket_code' => $ticketCode,
                'transaction_id' => $transaction->id,
                'woocommerce_order_id' => $order['id'],
                'schedule_id' => $schedule->id,
                'passenger_type' => 'adult',
                'seat_number' => $seatNumber,  // UPDATED: Use seat from WooCommerce
                'destination' => $schedule->destination->departure_location . ' → ' . $schedule->destination->destination_location
            ]);

            Ticket::create([
                'woocommerce_line_item_id' => $parsedData['line_item_id'],
                'ticket_code' => $ticketCode,
                'transaction_id' => $transaction->id,
                'passenger_name' => $passengerName,
                'passenger_type' => 'adult',
                'price' => $schedule->destination->adult_price,
                'qr_code' => $qrData,
                'status' => 'active',
                'seat_number' => $seatNumber,  // UPDATED: Directly use seat from WooCommerce
                'is_synced' => true,
                'synced_at' => now()
            ]);
        }

        // Create toddler tickets (no seat needed)
        for ($i = 0; $i < $toddlerCount; $i++) {
            $ticketIndex++;
            $passengerName = $seatMap[$ticketIndex] ?? $parsedData['passenger_name'] . ' (Balita ' . ($i + 1) . ')';

            $ticketCode = "WC-{$order['id']}-" . str_pad($ticketIndex, 3, '0', STR_PAD_LEFT);

            $qrData = json_encode([
                'ticket_code' => $ticketCode,
                'transaction_id' => $transaction->id,
                'woocommerce_order_id' => $order['id'],
                'schedule_id' => $schedule->id,
                'passenger_type' => 'toddler',
                'seat_number' => null, // Toddlers don't get seats
                'destination' => $schedule->destination->departure_location . ' → ' . $schedule->destination->destination_location
            ]);

            Ticket::create([
                'woocommerce_line_item_id' => $parsedData['line_item_id'],
                'ticket_code' => $ticketCode,
                'transaction_id' => $transaction->id,
                'passenger_name' => $passengerName,
                'passenger_type' => 'toddler',
                'price' => $schedule->destination->toddler_price ?? 0,
                'qr_code' => $qrData,
                'status' => 'active',
                'seat_number' => null, // Toddlers don't get seats - stays NULL
                'is_synced' => true,
                'synced_at' => now()
            ]);
        }
    }

    // UPDATED: Use seat numbers from WooCommerce (100% exact match)
    protected function createSeatBookings($parsedData, $transaction, $schedule)
    {
        $seatMap = $parsedData['seat_passenger_map'] ?? [];
        $seatNumbers = $parsedData['seat_numbers'] ?? [];  // NEW: Seat numbers from WooCommerce
        $adultCount = $parsedData['adult_count'];
        $toddlerCount = $parsedData['toddler_count'];

        // Only adults need seats - toddlers will be held on laps
        $requiredSeats = $adultCount;

        // Buat nama penumpang dewasa saja
        $adultPassengers = [];
        if (empty($seatMap)) {
            for ($i = 0; $i < $adultCount; $i++) {
                $adultPassengers[] = $parsedData['passenger_name'] . ($i > 0 ? ' (' . ($i + 1) . ')' : '');
            }
        } else {
            // Get first N adults from seat map
            $adultPassengers = array_slice(array_values($seatMap), 0, $adultCount);
        }

        // Get toddler passengers for seat booking record (without actual seats)
        $toddlerPassengers = [];
        for ($i = 0; $i < $toddlerCount; $i++) {
            $toddlerPassengers[] = $parsedData['passenger_name'] . ' (Balita ' . ($i + 1) . ')';
        }

        // Get already booked seats for this schedule and date
        $bookedSeats = SeatBooking::where('schedule_id', $schedule->id)
            ->where('departure_date', $parsedData['departure_date'])
            ->whereNotNull('seat_number')  // Only count actual seats
            ->pluck('seat_number')
            ->toArray();

        $assignedSeatNumbers = [];
        $useWooCommerceSeats = !empty($seatNumbers);

        if ($useWooCommerceSeats) {
            // NEW LOGIC: Use exact seat numbers from WooCommerce
            $this->info("🎯 Using seat numbers from WooCommerce: " . implode(', ', $seatNumbers));

            // Validate all seats from WooCommerce are available
            foreach ($seatNumbers as $index => $seatNumber) {
                if ($index > $adultCount) break; // Only process adult seats

                if (in_array($seatNumber, $bookedSeats)) {
                    // CONFLICT: Seat already booked offline!
                    throw new \Exception(
                        "Seat conflict! Seat {$seatNumber} from WooCommerce order is already booked locally. " .
                        "Schedule: #{$schedule->id}, Date: {$parsedData['departure_date']}. " .
                        "Please resolve this conflict manually."
                    );
                }
            }

            // All seats available, proceed with WooCommerce seat numbers
            foreach ($adultPassengers as $index => $passengerName) {
                $assignedSeat = $seatNumbers[$index + 1] ?? null;

                if (!$assignedSeat) {
                    // Fallback if WooCommerce didn't provide enough seats
                    $this->warn("⚠️  Seat not provided by WooCommerce for passenger #{$index}, auto-assigning...");
                    $availableSeats = $this->generateAvailableSeats($schedule, $bookedSeats);
                    $assignedSeat = $availableSeats[0] ?? null;

                    if (!$assignedSeat) {
                        throw new \Exception("No available seats for auto-assignment");
                    }
                }

                $assignedSeatNumbers[] = $assignedSeat;

                SeatBooking::create([
                    'schedule_id' => $schedule->id,
                    'departure_date' => $parsedData['departure_date'],
                    'seat_number' => $assignedSeat,
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $passengerName,
                    'passenger_type' => 'adult',
                    'status' => 'booked'
                ]);

                // Add to booked seats to avoid duplicate in same batch
                $bookedSeats[] = $assignedSeat;
            }

        } else {
            // FALLBACK: Auto-assign seats (old behavior for backward compatibility)
            $this->warn("⚠️  No seat numbers from WooCommerce, using auto-assignment (old behavior)");

            $availableSeats = $this->generateAvailableSeats($schedule, $bookedSeats);

            // VALIDATION: Check if we have enough available seats
            if (count($availableSeats) < $requiredSeats) {
                $availableCount = count($availableSeats);
                throw new \Exception(
                    "Insufficient seats available. Required: {$requiredSeats} (adults only), Available: {$availableCount} " .
                    "for schedule #{$schedule->id} on {$parsedData['departure_date']}"
                );
            }

            // Assign seats to adults only (auto-assign from front)
            foreach ($adultPassengers as $index => $passengerName) {
                $assignedSeat = $availableSeats[$index];
                $assignedSeatNumbers[] = $assignedSeat;

                SeatBooking::create([
                    'schedule_id' => $schedule->id,
                    'departure_date' => $parsedData['departure_date'],
                    'seat_number' => $assignedSeat,
                    'transaction_id' => $transaction->id,
                    'passenger_name' => $passengerName,
                    'passenger_type' => 'adult',
                    'status' => 'booked'
                ]);

                // Add to booked seats to avoid duplicate in same batch
                $bookedSeats[] = $assignedSeat;
            }
        }

        // Create seat booking records for toddlers (no actual seat - NULL seat_number)
        foreach ($toddlerPassengers as $passengerName) {
            SeatBooking::create([
                'schedule_id' => $schedule->id,
                'departure_date' => $parsedData['departure_date'],
                'seat_number' => null,  // Toddlers don't get seats
                'transaction_id' => $transaction->id,
                'passenger_name' => $passengerName,
                'passenger_type' => 'toddler',
                'status' => 'booked'
            ]);
        }

        // Update adult tickets with assigned seat numbers
        $adultTickets = $transaction->tickets()->where('passenger_type', 'adult')->get();
        foreach ($adultTickets as $i => $ticket) {
            if (isset($assignedSeatNumbers[$i])) {
                $ticket->update(['seat_number' => $assignedSeatNumbers[$i]]);
            }
        }

        // Toddler tickets remain with NULL seat_number (no update needed)

        $this->info("✅ Assigned seats to {$adultCount} adults: " . implode(', ', $assignedSeatNumbers));
        if ($toddlerCount > 0) {
            $this->info("   {$toddlerCount} toddler(s) - no seats assigned (will be held)");
        }
    }

    // Ini adalah FUNGSI BARU dari teman Anda (Dipertahankan)
    protected function generateAvailableSeats($schedule, $bookedSeats)
    {
        // Get existing seat numbers from any booking in the system for pattern detection
        $existingSeats = SeatBooking::where('schedule_id', $schedule->id)
            ->limit(50)
            ->pluck('seat_number')
            ->toArray();

        // If no existing bookings, check for same speedboat on different dates
        if (empty($existingSeats)) {
            $existingSeats = SeatBooking::whereHas('schedule', function ($q) use ($schedule) {
                $q->where('speedboat_id', $schedule->speedboat_id);
            })
                ->limit(50)
                ->pluck('seat_number')
                ->toArray();
        }

        $availableSeats = [];
        $capacity = $schedule->capacity ?? 50;

        if (!empty($existingSeats)) {
            // Detect pattern from existing seats
            $pattern = $this->detectSeatPattern($existingSeats);

            if ($pattern['type'] === 'alphanumeric') {
                // Pattern like A1, A2, B1, B2, or custom like ASS1, AX1, etc
                $rows = $pattern['rows'];
                $maxCol = $pattern['maxCol'];

                // Generate seats row by row, column by column (A1, A2, A3... B1, B2, B3...)
                foreach ($rows as $row) {
                    for ($col = 1; $col <= $maxCol; $col++) {
                        $seat = $row . $col;
                        if (!in_array($seat, $bookedSeats)) {
                            $availableSeats[] = $seat;
                        }

                        // Stop if we have enough seats
                        if (count($availableSeats) >= $capacity) {
                            break 2;
                        }
                    }
                }
            } else {
                // Numeric pattern: 1, 2, 3, 4...
                for ($i = 1; $i <= $capacity; $i++) {
                    $seat = (string) $i;
                    if (!in_array($seat, $bookedSeats)) {
                        $availableSeats[] = $seat;
                    }
                }
            }
        } else {
            // No pattern found, use default A1-A5, B1-B5 format (5 seats per row)
            $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
            $colsPerRow = 5;

            foreach ($rows as $row) {
                for ($col = 1; $col <= $colsPerRow; $col++) {
                    $seat = $row . $col;
                    if (!in_array($seat, $bookedSeats)) {
                        $availableSeats[] = $seat;
                    }

                    // Stop if we have enough seats
                    if (count($availableSeats) >= $capacity) {
                        break 2;
                    }
                }
            }
        }

        return $availableSeats;
    }

    // Ini adalah FUNGSI BARU dari teman Anda (Dipertahankan)
    protected function detectSeatPattern($seatNumbers)
    {
        $pattern = [
            'type' => 'alphanumeric',
            'rows' => [],
            'maxCol' => 5
        ];

        $rows = [];
        $maxCol = 0;

        foreach ($seatNumbers as $seat) {
            // Check if alphanumeric (A1, B2, AA1, etc)
            if (preg_match('/^([A-Z]+)(\d+)$/', $seat, $matches)) {
                $row = $matches[1];
                $col = (int) $matches[2];

                if (!in_array($row, $rows)) {
                    $rows[] = $row;
                }

                if ($col > $maxCol) {
                    $maxCol = $col;
                }
            } elseif (ctype_digit($seat)) {
                // Numeric only
                $pattern['type'] = 'numeric';
            }
        }

        // Sort rows alphabetically
        sort($rows);

        $pattern['rows'] = $rows;
        $pattern['maxCol'] = $maxCol > 0 ? $maxCol : 5;

        return $pattern;
    }
}