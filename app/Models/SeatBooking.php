<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'departure_date',
        'seat_number',
        'transaction_id',
        'passenger_name',
        'passenger_type',
        'status'
    ];

    protected $casts = [
        'departure_date' => 'date'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Helper method to generate seat layout for a schedule
    public static function generateSeatLayout($scheduleId, $departureDate, $capacity)
    {
        $seatsPerRow = 4;
        $totalRows = ceil($capacity / $seatsPerRow);
        $seatLabels = ['A', 'B', 'C', 'D'];
        
        $layout = [];
        
        // Get booked seats
        $bookedSeats = self::where('schedule_id', $scheduleId)
            ->where('departure_date', $departureDate)
            ->where('status', 'booked')
            ->pluck('seat_number')
            ->toArray();
        
        for ($row = 1; $row <= $totalRows; $row++) {
            $rowSeats = [];
            foreach ($seatLabels as $label) {
                $seatNumber = $label . $row;
                $rowSeats[] = [
                    'seat_number' => $seatNumber,
                    'is_available' => !in_array($seatNumber, $bookedSeats),
                    'passenger_info' => null
                ];
                
                // Break if we've reached capacity
                if (count($layout) * $seatsPerRow + count($rowSeats) >= $capacity) {
                    break;
                }
            }
            $layout[] = $rowSeats;
            
            // Break if we've reached capacity
            if (count($layout) * $seatsPerRow >= $capacity) {
                break;
            }
        }
        
        return $layout;
    }
}
