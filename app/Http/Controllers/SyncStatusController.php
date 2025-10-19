<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\SyncQueue;
use App\Models\SyncLog;
use App\Services\WooCommerceService;
use Illuminate\Support\Facades\Artisan;

class SyncStatusController extends Controller
{
    protected $woocommerce;

    public function __construct(WooCommerceService $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    public function index()
    {
        // Get sync statistics
        $syncedCount = Transaction::where('is_synced', true)->count();
        $pendingCount = Transaction::where('is_synced', false)
            ->whereNull('woocommerce_order_id')
            ->count();
        $failedCount = Transaction::whereNotNull('sync_error')->count();

        // Get total transactions
        $totalTransactions = Transaction::count();

        // Get last sync time
        $lastSyncTransaction = Transaction::where('is_synced', true)
            ->whereNotNull('synced_at')
            ->orderBy('synced_at', 'desc')
            ->first();
        $lastSyncTime = $lastSyncTransaction ? $lastSyncTransaction->synced_at : null;

        // Get failed transactions
        $failedTransactions = Transaction::whereNotNull('sync_error')
            ->with(['schedule.speedboat', 'schedule.destination'])
            ->latest()
            ->limit(10)
            ->get();

        // Get recent synced transactions
        $recentSynced = Transaction::where('is_synced', true)
            ->with(['schedule.speedboat', 'schedule.destination'])
            ->whereNotNull('synced_at')
            ->orderBy('synced_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending transactions
        $pendingTransactions = Transaction::where('is_synced', false)
            ->whereNull('woocommerce_order_id')
            ->whereNull('sync_error')
            ->with(['schedule.speedboat', 'schedule.destination'])
            ->latest()
            ->limit(10)
            ->get();

        // Check WooCommerce connection
        $connectionStatus = $this->woocommerce->checkConnection();

        // Get sync queue status
        $queuePending = SyncQueue::where('status', 'pending')->count();
        $queueFailed = SyncQueue::where('status', 'failed')->count();

        return view('sync.status', compact(
            'syncedCount',
            'pendingCount',
            'failedCount',
            'totalTransactions',
            'lastSyncTime',
            'failedTransactions',
            'recentSynced',
            'pendingTransactions',
            'connectionStatus',
            'queuePending',
            'queueFailed'
        ));
    }

    public function syncFrom()
    {
        try {
            Artisan::call('woocommerce:sync-from', ['--limit' => 20]);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Sync from WooCommerce completed',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncTo()
    {
        try {
            Artisan::call('woocommerce:sync-to');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Sync to WooCommerce completed',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function retry($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            // Clear previous error
            $transaction->update(['sync_error' => null]);

            // Try to sync
            Artisan::call('woocommerce:sync-to');

            // Check if sync was successful
            $transaction->refresh();

            if ($transaction->is_synced) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction synced successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $transaction->sync_error ?? 'Sync failed'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Retry failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        // Get filter parameters
        $syncType = $request->get('sync_type');
        $status = $request->get('status');
        $entityType = $request->get('entity_type');
        $days = $request->get('days', 7);

        // Build query
        $query = SyncLog::query()
            ->with(['triggeredBy'])
            ->recent($days);

        if ($syncType) {
            $query->where('sync_type', $syncType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($entityType) {
            $query->where('entity_type', $entityType);
        }

        // Get logs with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get statistics
        $stats = SyncLog::getStats($days);

        // Get daily summary for chart
        $dailySummary = SyncLog::getDailySummary($days);

        return view('sync.history', compact('logs', 'stats', 'dailySummary'));
    }

    public function exportHistory(Request $request)
    {
        $days = $request->get('days', 7);

        $logs = SyncLog::query()
            ->with(['triggeredBy'])
            ->recent($days)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'sync_logs_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Date Time',
                'Sync Type',
                'Entity Type',
                'Entity ID',
                'Status',
                'WooCommerce ID',
                'Error Message',
                'Duration (s)',
                'Triggered By',
                'Source'
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->sync_type,
                    $log->entity_type,
                    $log->entity_id ?? '-',
                    $log->status,
                    $log->woocommerce_id ?? '-',
                    $log->error_message ?? '-',
                    $log->duration_seconds ?? '-',
                    $log->triggeredBy->name ?? 'System',
                    $log->trigger_source
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
