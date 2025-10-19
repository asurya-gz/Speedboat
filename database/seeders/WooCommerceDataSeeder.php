<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Speedboat;
use App\Models\Destination;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class WooCommerceDataSeeder extends Seeder
{
    /**
     * Seed data from WooCommerce to local database
     */
    public function run()
    {
        $this->command->info('🚀 Starting WooCommerce data seeding...');

        DB::beginTransaction();
        try {
            // Create Destinations first
            $this->seedDestinations();

            // Create Speedboats with WooCommerce mapping
            $this->seedSpeedboats();

            // Create Schedules
            $this->seedSchedules();

            DB::commit();
            $this->command->info('✅ WooCommerce data seeded successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function seedDestinations()
    {
        $this->command->info('📍 Creating destinations...');

        $destinations = [
            [
                'departure_location' => 'TANJUNG SELOR',
                'destination_location' => 'TARAKAN',
                'code' => 'TS-TRK',
                'adult_price' => 150000,
                'child_price' => 0,
                'toddler_price' => 0,
                'description' => 'Pelabuhan Kayan II → Pelabuhan Tengkayu (SDF)',
                'is_active' => true
            ],
            [
                'departure_location' => 'TARAKAN',
                'destination_location' => 'TANJUNG SELOR',
                'code' => 'TRK-TS',
                'adult_price' => 150000,
                'child_price' => 0,
                'toddler_price' => 0,
                'description' => 'Pelabuhan Tengkayu (SDF) → Pelabuhan Kayan II',
                'is_active' => true
            ]
        ];

        foreach ($destinations as $dest) {
            Destination::updateOrCreate(
                [
                    'departure_location' => $dest['departure_location'],
                    'destination_location' => $dest['destination_location']
                ],
                $dest
            );
        }

        $this->command->info('✅ Created ' . count($destinations) . ' destinations');
    }

    protected function seedSpeedboats()
    {
        $this->command->info('🚤 Creating speedboats with WooCommerce mapping...');

        // Data from WooCommerce orders analysis
        $speedboats = [
            [
                'name' => 'SB. MENARA BARU',
                'code' => 'MB-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5964,
                'woocommerce_bus_id' => '64'
            ],
            [
                'name' => 'SB. ANDALAS 06',
                'code' => 'AN-006',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5961,
                'woocommerce_bus_id' => '161'
            ],
            [
                'name' => 'SB. MENARA NIKLAS',
                'code' => 'MN-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 6126,
                'woocommerce_bus_id' => '5934'
            ],
            [
                'name' => 'SB. ANDALAS 88',
                'code' => 'AN-088',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5949,
                'woocommerce_bus_id' => '133'
            ],
            [
                'name' => 'SB. ANUGERAH MANDIRI',
                'code' => 'AM-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5950,
                'woocommerce_bus_id' => '136'
            ],
            [
                'name' => 'SB. KALIMANTAN',
                'code' => 'KL-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5948,
                'woocommerce_bus_id' => '122'
            ],
            [
                'name' => 'SB. LIMEX FAMILY',
                'code' => 'LF-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5951,
                'woocommerce_bus_id' => '138'
            ],
            [
                'name' => 'SB. LIMEX KALTARA',
                'code' => 'LK-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5952,
                'woocommerce_bus_id' => '140'
            ],
            [
                'name' => 'SB. LIMEX MANORA',
                'code' => 'LM-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5954,
                'woocommerce_bus_id' => '144'
            ],
            [
                'name' => 'SB. SINAR HARAPAN FAMILY',
                'code' => 'SH-001',
                'capacity' => 50,
                'type' => 'Fast Ferry',
                'description' => 'Speedboat reguler',
                'is_active' => true,
                'woocommerce_product_id' => 5960,
                'woocommerce_bus_id' => '3077'
            ]
        ];

        foreach ($speedboats as $boat) {
            Speedboat::updateOrCreate(
                ['name' => $boat['name']],
                $boat
            );
        }

        $this->command->info('✅ Created ' . count($speedboats) . ' speedboats with WooCommerce mapping');
    }

    protected function seedSchedules()
    {
        $this->command->info('📅 Creating schedules...');

        $destination = Destination::where('departure_location', 'TANJUNG SELOR')
            ->where('destination_location', 'TARAKAN')
            ->first();

        if (!$destination) {
            $this->command->error('Destination not found!');
            return;
        }

        // Common departure times based on WooCommerce data
        $departureTimes = [
            '06:50', '07:15', '07:40', '08:05', '08:30', '08:55',
            '09:55', '10:35', '11:30', '14:20', '15:35'
        ];

        $speedboats = Speedboat::all();
        $createdSchedules = 0;

        foreach ($speedboats as $speedboat) {
            // Create 2-3 random schedules for each speedboat
            $numSchedules = rand(2, 3);
            $selectedTimes = array_rand(array_flip($departureTimes), min($numSchedules, count($departureTimes)));
            $selectedTimes = is_array($selectedTimes) ? $selectedTimes : [$selectedTimes];

            foreach ($selectedTimes as $time) {
                Schedule::updateOrCreate(
                    [
                        'speedboat_id' => $speedboat->id,
                        'destination_id' => $destination->id,
                        'departure_time' => $time
                    ],
                    [
                        'name' => $speedboat->name . ' - ' . $time,
                        'capacity' => $speedboat->capacity,
                        'is_active' => true,
                        'rows' => 13,
                        'columns' => 4,
                        'seat_numbers' => null
                    ]
                );
                $createdSchedules++;
            }
        }

        $this->command->info('✅ Created ' . $createdSchedules . ' schedules');
    }
}
