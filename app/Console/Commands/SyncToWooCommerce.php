<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WooCommerceService;
use App\Models\Transaction;
use App\Models\SyncQueue;
use App\Models\SyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncToWooCommerce extends Command
{
    protected $signature = 'woocommerce:sync-to
                            {--force : Force sync even if already synced}
                            {--retry : Retry failed syncs}';

    protected $description = 'Sync local transactions to WooCommerce (Offline → Online)';

    protected $woocommerce;

    public function __construct(WooCommerceService $woocommerce)
    {
        parent::__construct();
        $this->woocommerce = $woocommerce;
    }

    public function handle()
    {
        $this->info('🔄 Starting WooCommerce sync (Offline → Online)...');

        // Check connection first
        if (!$this->woocommerce->checkConnection()) {
            $this->warn('⚠️  Cannot connect to WooCommerce API. Queueing transactions for later...');
            $this->queuePendingTransactions();
            return 1;
        }

        $this->info('✅ Connection to WooCommerce established');

        // Get transactions to sync
        $query = Transaction::with(['schedule.destination', 'schedule.speedboat', 'tickets'])
            ->whereNull('woocommerce_order_id');

        if (!$this->option('force')) {
            $query->where('is_synced', false);
        }

        $transactions = $query->get();

        if ($transactions->isEmpty()) {
            $this->info('✅ No transactions to sync');

            // Process retry queue if requested
            if ($this->option('retry')) {
                $this->processRetryQueue();
            }

            return 0;
        }

        $this->info("Found {$transactions->count()} transactions to sync");

        $synced = 0;
        $failed = 0;

        foreach ($transactions as $transaction) {
            $startTime = microtime(true);
            $logData = [
                'sync_type' => 'sync_to',
                'entity_type' => 'transaction',
                'entity_id' => $transaction->id,
                'trigger_source' => 'auto',
            ];

            try {
                $this->info("Syncing transaction {$transaction->transaction_code}...");

                // Validate transaction has all required data
                if (!$this->validateTransaction($transaction)) {
                    $this->warn("⚠️  Transaction {$transaction->transaction_code} has incomplete data, skipping...");

                    // Log validation failure
                    SyncLog::createLog(array_merge($logData, [
                        'status' => 'failed',
                        'error_message' => 'Transaction validation failed: incomplete data',
                        'duration_seconds' => microtime(true) - $startTime,
                    ]));

                    continue;
                }

                // Format transaction for WooCommerce
                $orderData = $this->woocommerce->formatTransactionForWooCommerce($transaction);

                // Create order in WooCommerce
                $response = $this->woocommerce->createOrder($orderData);

                if ($response['success']) {
                    $woocommerceOrder = $response['data'];

                    // Update transaction with WooCommerce order ID
                    $transaction->update([
                        'woocommerce_order_id' => $woocommerceOrder['id'],
                        'is_synced' => true,
                        'synced_at' => now(),
                        'sync_error' => null
                    ]);

                    // Update tickets
                    if (isset($woocommerceOrder['line_items'][0])) {
                        $transaction->tickets()->update([
                            'woocommerce_line_item_id' => $woocommerceOrder['line_items'][0]['id'],
                            'is_synced' => true,
                            'synced_at' => now()
                        ]);
                    }

                    // Log success
                    SyncLog::createLog(array_merge($logData, [
                        'status' => 'success',
                        'woocommerce_id' => $woocommerceOrder['id'],
                        'request_data' => $orderData,
                        'response_data' => $woocommerceOrder,
                        'http_status_code' => 201,
                        'duration_seconds' => microtime(true) - $startTime,
                    ]));

                    $this->info("✅ Synced {$transaction->transaction_code} → WooCommerce Order #{$woocommerceOrder['id']}");
                    $synced++;

                } else {
                    throw new \Exception($response['error']);
                }

            } catch (\Exception $e) {
                $this->error("❌ Failed to sync {$transaction->transaction_code}: " . $e->getMessage());

                // Update transaction with error
                $transaction->update([
                    'sync_error' => substr($e->getMessage(), 0, 255)
                ]);

                // Log failure
                SyncLog::createLog(array_merge($logData, [
                    'status' => 'failed',
                    'request_data' => $orderData ?? null,
                    'error_message' => $e->getMessage(),
                    'http_status_code' => $response['http_code'] ?? null,
                    'duration_seconds' => microtime(true) - $startTime,
                ]));

                // Add to queue for retry
                SyncQueue::create([
                    'syncable_type' => Transaction::class,
                    'syncable_id' => $transaction->id,
                    'direction' => 'to_woocommerce',
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'retry_count' => 0,
                    'last_attempted_at' => now()
                ]);

                Log::error('Sync to WooCommerce failed', [
                    'transaction_id' => $transaction->id,
                    'transaction_code' => $transaction->transaction_code,
                    'error' => $e->getMessage()
                ]);

                $failed++;
            }
        }

        $this->newLine();
        $this->info("📊 Sync Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Synced', $synced],
                ['Failed', $failed],
            ]
        );

        // Process retry queue if requested
        if ($this->option('retry') && $failed == 0) {
            $this->processRetryQueue();
        }

        return 0;
    }

    protected function validateTransaction($transaction)
    {
        // Check if speedboat has WooCommerce product mapping
        if (!$transaction->schedule->speedboat->woocommerce_product_id) {
            $this->warn("Speedboat {$transaction->schedule->speedboat->name} doesn't have WooCommerce product mapping");
            return false;
        }

        // Check if transaction has tickets
        if ($transaction->tickets->isEmpty()) {
            $this->warn("Transaction {$transaction->transaction_code} has no tickets");
            return false;
        }

        return true;
    }

    protected function queuePendingTransactions()
    {
        $transactions = Transaction::whereNull('woocommerce_order_id')
            ->where('is_synced', false)
            ->get();

        foreach ($transactions as $transaction) {
            SyncQueue::firstOrCreate([
                'syncable_type' => Transaction::class,
                'syncable_id' => $transaction->id,
                'direction' => 'to_woocommerce',
            ], [
                'status' => 'pending',
                'retry_count' => 0
            ]);
        }

        $this->info("✅ Queued {$transactions->count()} transactions for sync when online");
    }

    protected function processRetryQueue()
    {
        $this->newLine();
        $this->info('🔄 Processing retry queue...');

        $queueItems = SyncQueue::where('direction', 'to_woocommerce')
            ->retryable(3)
            ->limit(10)
            ->get();

        if ($queueItems->isEmpty()) {
            $this->info('✅ No items in retry queue');
            return;
        }

        $this->info("Found {$queueItems->count()} items to retry");

        $retried = 0;
        $failed = 0;

        foreach ($queueItems as $item) {
            try {
                $item->update([
                    'status' => 'processing',
                    'last_attempted_at' => now()
                ]);

                $transaction = $item->syncable;

                if (!$transaction) {
                    $item->update(['status' => 'completed']);
                    continue;
                }

                // Try to sync again
                $orderData = $this->woocommerce->formatTransactionForWooCommerce($transaction);
                $response = $this->woocommerce->createOrder($orderData);

                if ($response['success']) {
                    $woocommerceOrder = $response['data'];

                    $transaction->update([
                        'woocommerce_order_id' => $woocommerceOrder['id'],
                        'is_synced' => true,
                        'synced_at' => now(),
                        'sync_error' => null
                    ]);

                    $item->update(['status' => 'completed']);

                    $this->info("✅ Retried successfully: {$transaction->transaction_code}");
                    $retried++;

                } else {
                    throw new \Exception($response['error']);
                }

            } catch (\Exception $e) {
                $item->increment('retry_count');
                $item->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);

                $this->error("❌ Retry failed: " . $e->getMessage());
                $failed++;
            }
        }

        $this->table(
            ['Status', 'Count'],
            [
                ['Retried Successfully', $retried],
                ['Failed Again', $failed],
            ]
        );
    }
}
