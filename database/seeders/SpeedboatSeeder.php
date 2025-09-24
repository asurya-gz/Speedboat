<?php

namespace Database\Seeders;

use App\Models\Speedboat;
use Illuminate\Database\Seeder;

class SpeedboatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $speedboats = [
            [
                'name' => 'Speedboat Express 1',
                'code' => 'SB-EX001',
                'capacity' => 25,
                'type' => 'Express',
                'description' => 'Speedboat express dengan kecepatan tinggi untuk perjalanan cepat ke pulau-pulau',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Express 2',
                'code' => 'SB-EX002',
                'capacity' => 30,
                'type' => 'Express',
                'description' => 'Speedboat express kapasitas besar dengan fasilitas AC dan sound system',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Comfort 1',
                'code' => 'SB-CF001',
                'capacity' => 20,
                'type' => 'Comfort',
                'description' => 'Speedboat comfort dengan tempat duduk empuk dan atap pelindung',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Comfort 2',
                'code' => 'SB-CF002',
                'capacity' => 22,
                'type' => 'Comfort',
                'description' => 'Speedboat comfort dilengkapi life jacket dan peralatan keselamatan lengkap',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Economy 1',
                'code' => 'SB-EC001',
                'capacity' => 35,
                'type' => 'Economy',
                'description' => 'Speedboat ekonomis dengan kapasitas besar untuk rombongan',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Economy 2',
                'code' => 'SB-EC002',
                'capacity' => 40,
                'type' => 'Economy',
                'description' => 'Speedboat ekonomis terbesar dengan harga terjangkau',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat VIP 1',
                'code' => 'SB-VIP001',
                'capacity' => 15,
                'type' => 'VIP',
                'description' => 'Speedboat VIP eksklusif dengan pelayanan premium dan fasilitas mewah',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat VIP 2',
                'code' => 'SB-VIP002',
                'capacity' => 12,
                'type' => 'VIP',
                'description' => 'Speedboat VIP private dengan kabin ber-AC dan mini bar',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Tourist 1',
                'code' => 'SB-TR001',
                'capacity' => 28,
                'type' => 'Tourist',
                'description' => 'Speedboat wisata dengan pemanduan dan penjelasan destinasi',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Tourist 2',
                'code' => 'SB-TR002',
                'capacity' => 24,
                'type' => 'Tourist',
                'description' => 'Speedboat wisata dilengkapi kamera underwater dan guide berpengalaman',
                'is_active' => true
            ],
            [
                'name' => 'Speedboat Maintenance',
                'code' => 'SB-MNT001',
                'capacity' => 18,
                'type' => 'Standard',
                'description' => 'Speedboat cadangan untuk maintenance dan emergency',
                'is_active' => false
            ]
        ];

        foreach ($speedboats as $speedboat) {
            Speedboat::create($speedboat);
        }
    }
}