<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speedboat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'capacity',
        'type',
        'description',
        'is_active',
        'woocommerce_product_id',
        'woocommerce_bus_id'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
