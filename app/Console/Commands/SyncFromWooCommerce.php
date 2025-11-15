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
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncFromWooCommerce extends Command
{
    protected $signature = 'woocommerce:sync-from
                            {--since= : Sync orders since this date (Y-m-d)}
                            {--limit=20 : Number of orders to fetch}';

    protected $description = 'Sync orders from WooCommerce to local database';

    protected $woocommerce;

    public function __construct(WooCommerceService $woocommerce)
    {
        parent::__construct();
        $this->woocommerce = $woocommerce;
    }

    public function handle()
    {
        $this->info('🔄 Starting WooCommerce sync (Online → Offline)...');

        // Check connection first
        if (!$this->woocommerce->checkConnection()) {
            $this->error('❌ Cannot connect to WooCommerce API. Check your internet connection.');
            return 1;
        }

        $this->info('✅ Connection to WooCommerce established');

        // First, sync master data (speedboats, destinations, schedules)
        $this->newLine();
        $this->info('📦 Step 1: Syncing master data...');
        $this->call('woocommerce:sync-master-data');
        $this->newLine();
        $this->info('📋 Step 2: Syncing transactions...');

        // Prepare query parameters
        $params = [
            'per_page' => $this->option('limit'),
            'orderby' => 'date',
            'order' => 'desc'
        ];

        if ($this->option('since')) {
            $params['after'] = Carbon::parse($this->option('since'))->toIso8601String();
        }

        // Fetch orders from WooCommerce
        $this->info('📥 Fetching orders from WooCommerce...');
        $response = $this->woocommerce->getOrders($params);

        if (!$response['success']) {
            $this->error('❌ Failed to fetch orders: ' . $response['error']);
            return 1;
        }

        $orders = $response['data'];
        $ordersCount = is_array($orders) ? count($orders) : $orders->count();
        $this->info("Found {$ordersCount} orders to process");

        $synced = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($orders as $order) {
            $startTime = microtime(true);
            $logData = [
                'sync_type' => 'sync_from',
                'entity_type' => 'transaction',
                'trigger_source' => 'auto',
                'woocommerce_id' => $order['id'],
            ];

            try {
                // Skip if already synced
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

                    // Log success
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

                // Log failure
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

    protected function findOrCreateSchedule($parsedData)
    {
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
        $requiredSeats = count($seatMap);

        // Get already booked seats for this schedule and date
        $bookedSeats = SeatBooking::where('schedule_id', $schedule->id)
            ->where('departure_date', $parsedData['departure_date'])
            ->pluck('seat_number')
            ->toArray();

        // DYNAMIC: Get seat format from existing bookings or generate based on capacity
        $availableSeats = $this->generateAvailableSeats($schedule, $bookedSeats);

        // VALIDATION: Check if we have enough available seats
        if (count($availableSeats) < $requiredSeats) {
            $availableCount = count($availableSeats);
            throw new \Exception(
                "Insufficient seats available. Required: {$requiredSeats}, Available: {$availableCount} " .
                "for schedule #{$schedule->id} on {$parsedData['departure_date']}"
            );
        }

        $seatIndex = 0;
        foreach ($seatMap as $passengerIndex => $passengerName) {
            // Auto-assign from available seats (prioritize front seats)
            $assignedSeat = $availableSeats[$seatIndex];
            $seatIndex++;

            SeatBooking::create([
                'schedule_id' => $schedule->id,
                'departure_date' => $parsedData['departure_date'],
                'seat_number' => $assignedSeat,
                'transaction_id' => $transaction->id,
                'passenger_name' => $passengerName,
                'passenger_type' => $parsedData['passenger_type'],
                'status' => 'booked'
            ]);

            // Add to booked seats to avoid duplicate in same batch
            $bookedSeats[] = $assignedSeat;
        }

        $this->info("✅ Assigned seats: " . implode(', ', array_slice($availableSeats, 0, $requiredSeats)));
    }

    /**
     * Generate available seats based on existing seat format pattern in database
     * This auto-detects seat format (A1, AA1, 1A, etc) from existing bookings
     * Returns seats in order from front to back
     */
    protected function generateAvailableSeats($schedule, $bookedSeats)
    {
        // Get existing seat numbers from any booking in the system for pattern detection
        $existingSeats = SeatBooking::where('schedule_id', $schedule->id)
            ->limit(50)
            ->pluck('seat_number')
            ->toArray();

        // If no existing bookings, check for same speedboat on different dates
        if (empty($existingSeats)) {
            $existingSeats = SeatBooking::whereHas('schedule', function($q) use ($schedule) {
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
                    $seat = (string)$i;
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

    /**
     * Detect seat numbering pattern from existing seat numbers
     */
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
                $col = (int)$matches[2];

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
