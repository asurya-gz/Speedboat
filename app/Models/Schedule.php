<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id',
        'name',
        'departure_time',
        'capacity',
        'is_active'
    ];

    protected $casts = [
        'departure_time' => 'datetime:H:i',
        'capacity' => 'integer',
        'is_active' => 'boolean'
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, Transaction::class);
    }
}
