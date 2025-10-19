<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_type',
        'entity_type',
        'entity_id',
        'status',
        'woocommerce_id',
        'request_data',
        'response_data',
        'error_message',
        'http_status_code',
        'duration_seconds',
        'triggered_by',
        'trigger_source',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'duration_seconds' => 'float',
    ];

    /**
     * Get the user who triggered this sync
     */
    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Get the related entity (transaction or speedboat)
     */
    public function entity()
    {
        if ($this->entity_type === 'transaction') {
            return $this->belongsTo(Transaction::class, 'entity_id');
        } elseif ($this->entity_type === 'speedboat') {
            return $this->belongsTo(Speedboat::class, 'entity_id');
        }
        return null;
    }

    /**
     * Scope for successful syncs
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed syncs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for sync from WooCommerce
     */
    public function scopeSyncFrom($query)
    {
        return $query->where('sync_type', 'sync_from');
    }

    /**
     * Scope for sync to WooCommerce
     */
    public function scopeSyncTo($query)
    {
        return $query->where('sync_type', 'sync_to');
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for transactions
     */
    public function scopeTransactions($query)
    {
        return $query->where('entity_type', 'transaction');
    }

    /**
     * Scope for speedboats/products
     */
    public function scopeProducts($query)
    {
        return $query->whereIn('entity_type', ['speedboat', 'product']);
    }

    /**
     * Get summary statistics
     */
    public static function getStats($days = 7)
    {
        $query = self::query()->recent($days);

        return [
            'total' => $query->count(),
            'successful' => (clone $query)->successful()->count(),
            'failed' => (clone $query)->failed()->count(),
            'sync_from' => (clone $query)->syncFrom()->count(),
            'sync_to' => (clone $query)->syncTo()->count(),
            'avg_duration' => (clone $query)->successful()->avg('duration_seconds'),
            'total_transactions' => (clone $query)->transactions()->count(),
            'total_products' => (clone $query)->products()->count(),
        ];
    }

    /**
     * Get daily summary
     */
    public static function getDailySummary($days = 30)
    {
        return self::query()
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as successful')
            ->selectRaw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            ->selectRaw('AVG(duration_seconds) as avg_duration')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Create a log entry
     */
    public static function createLog(array $data)
    {
        return self::create($data);
    }
}
