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
                'name' => 'Pulau Tidung',
                'code' => 'PTD',
                'adult_price' => 50000,
                'child_price' => 30000,
                'description' => 'Pulau cantik dengan jembatan cinta yang terkenal',
                'is_active' => true
            ],
            [
                'name' => 'Pulau Harapan',
                'code' => 'PHR',
                'adult_price' => 65000,
                'child_price' => 40000,
                'description' => 'Pulau dengan resort dan snorkeling terbaik',
                'is_active' => true
            ],
            [
                'name' => 'Pulau Pramuka',
                'code' => 'PPR',
                'adult_price' => 45000,
                'child_price' => 25000,
                'description' => 'Pulau dengan penangkaran penyu',
                'is_active' => true
            ],
            [
                'name' => 'Pulau Kelapa',
                'code' => 'PKL',
                'adult_price' => 55000,
                'child_price' => 35000,
                'description' => 'Pulau tenang dengan pantai berpasir putih',
                'is_active' => true
            ],
            [
                'name' => 'Pulau Untung Jawa',
                'code' => 'PUJ',
                'adult_price' => 40000,
                'child_price' => 20000,
                'description' => 'Pulau terdekat dari Marina Ancol',
                'is_active' => true
            ]
        ];

        foreach ($destinations as $destination) {
            Destination::create($destination);
        }
    }
}
