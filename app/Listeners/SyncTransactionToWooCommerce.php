<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Services\WooCommerceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SyncTransactionToWooCommerce implements ShouldQueue
{
    use InteractsWithQueue;

    protected $woocommerce;

    /**
     * Create the event listener.
     */
    public function __construct(WooCommerceService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        // Skip if transaction already synced or came from WooCommerce
        if ($transaction->is_synced || $transaction->woocommerce_order_id) {
            return;
        }

        try {
            Log::info('Real-time sync: Syncing transaction to WooCommerce', [
                'transaction_id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code
            ]);

            // Load relations
            $transaction->load(['schedule.speedboat', 'schedule.destination', 'tickets']);

            // Format transaction for WooCommerce
            $orderData = $this->woocommerce->formatTransactionForWooCommerce($transaction);

            // Create order in WooCommerce
            $response = $this->woocommerce->createOrder($orderData);

            if ($response['success']) {
                $woocommerceOrderId = $response['data']['id'];

                // Update transaction with WooCommerce order ID
                $transaction->update([
                    'woocommerce_order_id' => $woocommerceOrderId,
                    'is_synced' => true,
                    'synced_at' => now(),
                    'sync_error' => null
                ]);

                Log::info('Real-time sync: Successfully synced transaction', [
                    'transaction_id' => $transaction->id,
                    'woocommerce_order_id' => $woocommerceOrderId
                ]);
            } else {
                throw new \Exception($response['error'] ?? 'Unknown error');
            }

        } catch (\Exception $e) {
            // Save error for retry by scheduler
            $transaction->update([
                'sync_error' => $e->getMessage()
            ]);

            Log::error('Real-time sync: Failed to sync transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            // Re-throw to trigger queue retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(TransactionCreated $event, \Throwable $exception): void
    {
        $transaction = $event->transaction;

        Log::error('Real-time sync: Queue job failed permanently', [
            'transaction_id' => $transaction->id,
            'error' => $exception->getMessage()
        ]);

        $transaction->update([
            'sync_error' => 'Real-time sync failed: ' . $exception->getMessage()
        ]);
    }
}
