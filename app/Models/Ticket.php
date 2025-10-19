<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'transaction_id',
        'passenger_name',
        'passenger_type',
        'price',
        'qr_code',
        'status',
        'seat_number',
        'boarding_time',
        'validated_at',
        'validated_by',
        'is_synced',
        'woocommerce_line_item_id',
        'synced_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'boarding_time' => 'datetime',
        'validated_at' => 'datetime',
        'is_synced' => 'boolean',
        'synced_at' => 'datetime'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isValidated()
    {
        return !is_null($this->validated_at);
    }
}
