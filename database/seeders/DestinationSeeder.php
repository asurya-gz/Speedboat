<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $destinations = [
            [
                'code' => 'PTD',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Tidung',
                'adult_price' => 50000,
                'toddler_price' => 15000,
                'description' => 'Pulau cantik dengan jembatan cinta yang terkenal di Kepulauan Seribu',
                'is_active' => true
            ],
            [
                'code' => 'PHR',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Harapan',
                'adult_price' => 65000,
                'toddler_price' => 20000,
                'description' => 'Pulau dengan resort dan snorkeling terbaik di Kepulauan Seribu',
                'is_active' => true
            ],
            [
                'code' => 'PPR',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Pramuka',
                'adult_price' => 45000,
                'toddler_price' => 12000,
                'description' => 'Pulau dengan penangkaran penyu dan pusat pemerintahan Kepulauan Seribu',
                'is_active' => true
            ],
            [
                'code' => 'PKL',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Kelapa',
                'adult_price' => 55000,
                'toddler_price' => 18000,
                'description' => 'Pulau tenang dengan pantai berpasir putih dan air jernih',
                'is_active' => true
            ],
            [
                'code' => 'PUJ',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Untung Jawa',
                'adult_price' => 40000,
                'toddler_price' => 10000,
                'description' => 'Pulau terdekat dari Marina Ancol, cocok untuk wisata keluarga',
                'is_active' => true
            ],
            [
                'code' => 'PKP',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Kelor dan Pulau Onrust',
                'adult_price' => 42000,
                'toddler_price' => 11000,
                'description' => 'Wisata sejarah benteng VOC dan pulau kecil yang eksotis',
                'is_active' => true
            ],
            [
                'code' => 'PBD',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Bidadari',
                'adult_price' => 70000,
                'toddler_price' => 22000,
                'description' => 'Pulau resort mewah dengan fasilitas lengkap dan cottage',
                'is_active' => true
            ],
            [
                'code' => 'PKY',
                'departure_location' => 'Marina Ancol',
                'destination_location' => 'Pulau Kayangan',
                'adult_price' => 60000,
                'toddler_price' => 19000,
                'description' => 'Pulau dengan pemandangan sunset terbaik di Kepulauan Seribu',
                'is_active' => true
            ],
            [
                'code' => 'PSB',
                'departure_location' => 'Pelabuhan Muara Angke',
                'destination_location' => 'Pulau Sebira',
                'adult_price' => 48000,
                'toddler_price' => 14000,
                'description' => 'Pulau dengan fishing spot terbaik dan wisata memancing',
                'is_active' => true
            ],
            [
                'code' => 'PAY',
                'departure_location' => 'Pelabuhan Muara Angke',
                'destination_location' => 'Pulau Ayer',
                'adult_price' => 75000,
                'toddler_price' => 25000,
                'description' => 'Pulau resort eksklusif dengan cottage di atas air',
                'is_active' => true
            ]
        ];

        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
