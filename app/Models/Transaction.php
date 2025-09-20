<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'schedule_id',
        'passenger_name',
        'adult_count',
        'child_count',
        'toddler_count',
        'total_amount',
        'payment_method',
        'payment_status',
        'is_synced',
        'created_by',
        'notes',
        'paid_at',
        'payment_reference'
    ];

    protected $casts = [
        'adult_count' => 'integer',
        'child_count' => 'integer',
        'toddler_count' => 'integer',
        'total_amount' => 'decimal:2',
        'is_synced' => 'boolean',
        'paid_at' => 'datetime'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
