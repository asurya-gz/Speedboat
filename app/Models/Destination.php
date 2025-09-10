<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'adult_price',
        'child_price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
