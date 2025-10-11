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
        $schedule = Schedule::find($scheduleId);

        // Use schedule's configured layout if available
        $columns = $schedule->columns ?? 4;
        $rows = $schedule->rows ?? ceil($capacity / $columns);
        $seatNumbers = $schedule->seat_numbers ?? [];

        $layout = [];

        // Get booked seats
        $bookedSeats = self::where('schedule_id', $scheduleId)
            ->where('departure_date', $departureDate)
            ->where('status', 'booked')
            ->pluck('seat_number')
            ->toArray();

        $seatCount = 0;
        for ($row = 1; $row <= $rows; $row++) {
            $rowSeats = [];

            for ($col = 0; $col < $columns; $col++) {
                if ($seatCount >= $capacity) {
                    // Add empty placeholder for visual alignment
                    $rowSeats[] = [
                        'seat_number' => null,
                        'is_available' => false,
                        'is_empty' => true,
                        'passenger_info' => null
                    ];
                    continue;
                }

                $position = "{$row}-{$col}";

                // Get seat number from configured layout or generate default
                $seatNumber = $seatNumbers[$position] ?? self::generateDefaultSeatNumber($row, $col);

                $rowSeats[] = [
                    'seat_number' => $seatNumber,
                    'is_available' => !in_array($seatNumber, $bookedSeats),
                    'is_empty' => false,
                    'passenger_info' => null
                ];

                $seatCount++;
            }

            $layout[] = $rowSeats;
        }

        return $layout;
    }

    // Generate default seat number if not configured
    private static function generateDefaultSeatNumber($row, $col)
    {
        $seatLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seatLabel = $seatLabels[$col % strlen($seatLabels)];
        return $seatLabel . $row;
    }
}
