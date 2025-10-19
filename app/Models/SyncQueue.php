<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncQueue extends Model
{
    protected $table = 'sync_queue';

    protected $fillable = [
        'syncable_type',
        'syncable_id',
        'direction',
        'status',
        'payload',
        'error_message',
        'retry_count',
        'last_attempted_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'last_attempted_at' => 'datetime'
    ];

    /**
     * Get the parent syncable model (Transaction or Ticket)
     */
    public function syncable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for pending items
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed items that can be retried
     */
    public function scopeRetryable($query, $maxRetries = 3)
    {
        return $query->where('status', 'failed')
                    ->where('retry_count', '<', $maxRetries);
    }
}
