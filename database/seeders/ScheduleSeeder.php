<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            // Jadwal untuk Marina Ancol → Pulau Tidung
            [
                'destination_id' => 1, // Marina Ancol → Pulau Tidung
                'speedboat_id' => 1, // Speedboat Express 1
                'name' => 'Speedboat Express 1',
                'departure_time' => '08:00',
                'capacity' => 50,
                'columns' => 5,
                'rows' => 10,
                'seat_numbers' => $this->generateSeatNumbers(10, 5, 50),
                'is_active' => true
            ],
            [
                'destination_id' => 1, // Marina Ancol → Pulau Tidung
                'speedboat_id' => 2, // Speedboat Express 2
                'name' => 'Speedboat Express 2',
                'departure_time' => '13:00',
                'capacity' => 50,
                'columns' => 5,
                'rows' => 10,
                'seat_numbers' => $this->generateSeatNumbers(10, 5, 50),
                'is_active' => true
            ],

            // Jadwal untuk Marina Ancol → Pulau Harapan
            [
                'destination_id' => 2, // Marina Ancol → Pulau Harapan
                'speedboat_id' => 3,
                'name' => 'Speedboat Express 3',
                'departure_time' => '08:30',
                'capacity' => 45,
                'columns' => 5,
                'rows' => 9,
                'seat_numbers' => $this->generateSeatNumbers(9, 5, 45),
                'is_active' => true
            ],
            [
                'destination_id' => 2, // Marina Ancol → Pulau Harapan
                'speedboat_id' => 1,
                'name' => 'Speedboat Express 1',
                'departure_time' => '15:00',
                'capacity' => 45,
                'columns' => 5,
                'rows' => 9,
                'seat_numbers' => $this->generateSeatNumbers(9, 5, 45),
                'is_active' => true
            ],

            // Jadwal untuk Marina Ancol → Pulau Pramuka
            [
                'destination_id' => 3, // Marina Ancol → Pulau Pramuka
                'speedboat_id' => 2,
                'name' => 'Speedboat Express 2',
                'departure_time' => '09:00',
                'capacity' => 40,
                'columns' => 4,
                'rows' => 10,
                'seat_numbers' => $this->generateSeatNumbers(10, 4, 40),
                'is_active' => true
            ],
            [
                'destination_id' => 3, // Marina Ancol → Pulau Pramuka
                'speedboat_id' => 3,
                'name' => 'Speedboat Express 3',
                'departure_time' => '14:00',
                'capacity' => 40,
                'columns' => 4,
                'rows' => 10,
                'seat_numbers' => $this->generateSeatNumbers(10, 4, 40),
                'is_active' => false
            ],

            // Jadwal untuk Marina Ancol → Pulau Kelapa
            [
                'destination_id' => 4, // Marina Ancol → Pulau Kelapa
                'speedboat_id' => 1,
                'name' => 'Speedboat Express 1',
                'departure_time' => '07:30',
                'capacity' => 35,
                'columns' => 4,
                'rows' => 9,
                'seat_numbers' => $this->generateSeatNumbers(9, 4, 35),
                'is_active' => true
            ],
            [
                'destination_id' => 4, // Marina Ancol → Pulau Kelapa
                'speedboat_id' => 2,
                'name' => 'Speedboat Express 2',
                'departure_time' => '12:30',
                'capacity' => 35,
                'columns' => 4,
                'rows' => 9,
                'seat_numbers' => $this->generateSeatNumbers(9, 4, 35),
                'is_active' => true
            ],

            // Jadwal untuk Marina Ancol → Pulau Untung Jawa
            [
                'destination_id' => 5, // Marina Ancol → Pulau Untung Jawa
                'speedboat_id' => 3,
                'name' => 'Speedboat Express 3',
                'departure_time' => '07:00',
                'capacity' => 60,
                'columns' => 5,
                'rows' => 12,
                'seat_numbers' => $this->generateSeatNumbers(12, 5, 60),
                'is_active' => true
            ],
            [
                'destination_id' => 5, // Marina Ancol → Pulau Untung Jawa
                'speedboat_id' => 1,
                'name' => 'Speedboat Express 1',
                'departure_time' => '12:00',
                'capacity' => 60,
                'columns' => 5,
                'rows' => 12,
                'seat_numbers' => $this->generateSeatNumbers(12, 5, 60),
                'is_active' => true
            ],
            [
                'destination_id' => 5, // Marina Ancol → Pulau Untung Jawa
                'speedboat_id' => 2,
                'name' => 'Speedboat Express 2',
                'departure_time' => '16:00',
                'capacity' => 60,
                'columns' => 5,
                'rows' => 12,
                'seat_numbers' => $this->generateSeatNumbers(12, 5, 60),
                'is_active' => true
            ],

            // Jadwal untuk Marina Ancol → Pulau Kelor dan Pulau Onrust
            [
                'destination_id' => 6, // Marina Ancol → Pulau Kelor dan Pulau Onrust
                'speedboat_id' => 3,
                'name' => 'Speedboat Express 3',
                'departure_time' => '09:30',
                'capacity' => 25,
                'columns' => 4,
                'rows' => 7,
                'seat_numbers' => $this->generateSeatNumbers(7, 4, 25),
                'is_active' => true
            ],

            // Jadwal untuk Marina Ancol → Pulau Bidadari
            [
                'destination_id' => 7, // Marina Ancol → Pulau Bidadari
                'speedboat_id' => 1,
                'name' => 'Speedboat Express 1',
                'departure_time' => '08:00',
                'capacity' => 30,
                'columns' => 4,
                'rows' => 8,
                'seat_numbers' => $this->generateSeatNumbers(8, 4, 30),
                'is_active' => true
            ],
            [
                'destination_id' => 7, // Marina Ancol → Pulau Bidadari
                'speedboat_id' => 2,
                'name' => 'Speedboat Express 2',
                'departure_time' => '13:30',
                'capacity' => 30,
                'columns' => 4,
                'rows' => 8,
                'seat_numbers' => $this->generateSeatNumbers(8, 4, 30),
                'is_active' => false
            ],

            // Jadwal untuk Marina Ancol → Pulau Kayangan
            [
                'destination_id' => 8, // Marina Ancol → Pulau Kayangan
                'speedboat_id' => 3,
                'name' => 'Speedboat Express 3',
                'departure_time' => '15:30',
                'capacity' => 25,
                'columns' => 4,
                'rows' => 7,
                'seat_numbers' => $this->generateSeatNumbers(7, 4, 25),
                'is_active' => true
            ],

            // Jadwal untuk Pelabuhan Muara Angke → Pulau Sebira
            [
                'destination_id' => 9, // Pelabuhan Muara Angke → Pulau Sebira
                'speedboat_id' => 1,
                'name' => 'Speedboat Express 1',
                'departure_time' => '06:00',
                'capacity' => 20,
                'columns' => 4,
                'rows' => 5,
                'seat_numbers' => $this->generateSeatNumbers(5, 4, 20),
                'is_active' => true
            ],
            [
                'destination_id' => 9, // Pelabuhan Muara Angke → Pulau Sebira
                'speedboat_id' => 2,
                'name' => 'Speedboat Express 2',
                'departure_time' => '14:00',
                'capacity' => 20,
                'columns' => 4,
                'rows' => 5,
                'seat_numbers' => $this->generateSeatNumbers(5, 4, 20),
                'is_active' => true
            ],

            // Jadwal untuk Pelabuhan Muara Angke → Pulau Ayer
            [
                'destination_id' => 10, // Pelabuhan Muara Angke → Pulau Ayer
                'speedboat_id' => 3,
                'name' => 'Speedboat Express 3',
                'departure_time' => '08:00',
                'capacity' => 15,
                'columns' => 3,
                'rows' => 5,
                'seat_numbers' => $this->generateSeatNumbers(5, 3, 15),
                'is_active' => true
            ],
            [
                'destination_id' => 10, // Pelabuhan Muara Angke → Pulau Ayer
                'speedboat_id' => 1,
                'name' => 'Speedboat Express 1',
                'departure_time' => '13:00',
                'capacity' => 15,
                'columns' => 3,
                'rows' => 5,
                'seat_numbers' => $this->generateSeatNumbers(5, 3, 15),
                'is_active' => true
            ]
        ];

        foreach ($schedules as $schedule) {
            \App\Models\Schedule::create($schedule);
        }
    }

    /**
     * Generate seat numbers for a given layout
     */
    private function generateSeatNumbers(int $rows, int $columns, int $capacity): array
    {
        $seatNumbers = [];
        $seatLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seatCount = 0;

        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 0; $col < $columns; $col++) {
                if ($seatCount >= $capacity) break;

                $seatLabel = $seatLabels[$col % strlen($seatLabels)];
                $seatNumber = $seatLabel . $row;
                $position = "{$row}-{$col}";
                $seatNumbers[$position] = $seatNumber;
                $seatCount++;
            }
        }

        return $seatNumbers;
    }
}
