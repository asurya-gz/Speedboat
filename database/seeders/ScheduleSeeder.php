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
                'name' => 'Jadwal Pagi',
                'departure_time' => '08:00',
                'capacity' => 50,
                'is_active' => true
            ],
            [
                'destination_id' => 1, // Marina Ancol → Pulau Tidung
                'speedboat_id' => 2, // Speedboat Express 2
                'name' => 'Jadwal Siang',
                'departure_time' => '13:00',
                'capacity' => 50,
                'is_active' => true
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Harapan
            [
                'destination_id' => 2, // Marina Ancol → Pulau Harapan
                'name' => 'Jadwal Pagi',
                'departure_time' => '08:30',
                'capacity' => 45,
                'is_active' => true
            ],
            [
                'destination_id' => 2, // Marina Ancol → Pulau Harapan
                'name' => 'Jadwal Sore',
                'departure_time' => '15:00',
                'capacity' => 45,
                'is_active' => true
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Pramuka
            [
                'destination_id' => 3, // Marina Ancol → Pulau Pramuka
                'name' => 'Jadwal Pagi',
                'departure_time' => '09:00',
                'capacity' => 40,
                'is_active' => true
            ],
            [
                'destination_id' => 3, // Marina Ancol → Pulau Pramuka
                'name' => 'Jadwal Siang',
                'departure_time' => '14:00',
                'capacity' => 40,
                'is_active' => false
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Kelapa
            [
                'destination_id' => 4, // Marina Ancol → Pulau Kelapa
                'name' => 'Jadwal Pagi',
                'departure_time' => '07:30',
                'capacity' => 35,
                'is_active' => true
            ],
            [
                'destination_id' => 4, // Marina Ancol → Pulau Kelapa
                'name' => 'Jadwal Siang',
                'departure_time' => '12:30',
                'capacity' => 35,
                'is_active' => true
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Untung Jawa
            [
                'destination_id' => 5, // Marina Ancol → Pulau Untung Jawa
                'name' => 'Jadwal Pagi',
                'departure_time' => '07:00',
                'capacity' => 60,
                'is_active' => true
            ],
            [
                'destination_id' => 5, // Marina Ancol → Pulau Untung Jawa
                'name' => 'Jadwal Siang',
                'departure_time' => '12:00',
                'capacity' => 60,
                'is_active' => true
            ],
            [
                'destination_id' => 5, // Marina Ancol → Pulau Untung Jawa
                'name' => 'Jadwal Sore',
                'departure_time' => '16:00',
                'capacity' => 60,
                'is_active' => true
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Kelor dan Pulau Onrust
            [
                'destination_id' => 6, // Marina Ancol → Pulau Kelor dan Pulau Onrust
                'name' => 'Jadwal Wisata Sejarah',
                'departure_time' => '09:30',
                'capacity' => 25,
                'is_active' => true
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Bidadari
            [
                'destination_id' => 7, // Marina Ancol → Pulau Bidadari
                'name' => 'Jadwal Resort Pagi',
                'departure_time' => '08:00',
                'capacity' => 30,
                'is_active' => true
            ],
            [
                'destination_id' => 7, // Marina Ancol → Pulau Bidadari
                'name' => 'Jadwal Resort Siang',
                'departure_time' => '13:30',
                'capacity' => 30,
                'is_active' => false
            ],
            
            // Jadwal untuk Marina Ancol → Pulau Kayangan
            [
                'destination_id' => 8, // Marina Ancol → Pulau Kayangan
                'name' => 'Jadwal Sunset',
                'departure_time' => '15:30',
                'capacity' => 25,
                'is_active' => true
            ],
            
            // Jadwal untuk Pelabuhan Muara Angke → Pulau Sebira
            [
                'destination_id' => 9, // Pelabuhan Muara Angke → Pulau Sebira
                'name' => 'Jadwal Memancing Pagi',
                'departure_time' => '06:00',
                'capacity' => 20,
                'is_active' => true
            ],
            [
                'destination_id' => 9, // Pelabuhan Muara Angke → Pulau Sebira
                'name' => 'Jadwal Memancing Sore',
                'departure_time' => '14:00',
                'capacity' => 20,
                'is_active' => true
            ],
            
            // Jadwal untuk Pelabuhan Muara Angke → Pulau Ayer
            [
                'destination_id' => 10, // Pelabuhan Muara Angke → Pulau Ayer
                'name' => 'Jadwal Eksklusif Pagi',
                'departure_time' => '08:00',
                'capacity' => 15,
                'is_active' => true
            ],
            [
                'destination_id' => 10, // Pelabuhan Muara Angke → Pulau Ayer
                'name' => 'Jadwal Eksklusif Siang',
                'departure_time' => '13:00',
                'capacity' => 15,
                'is_active' => true
            ]
        ];

        foreach ($schedules as $schedule) {
            \App\Models\Schedule::create($schedule);
        }
    }
}
