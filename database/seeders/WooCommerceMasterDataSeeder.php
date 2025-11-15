<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Speedboat;
use App\Models\Destination;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class WooCommerceMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->command->info('🚤 Creating Speedboats...');

            // Create Speedboats matching WooCommerce data
            $speedboats = [
                ['name' => 'SB. KALIMANTAN', 'code' => 'KLMTN', 'capacity' => 50, 'is_active' => true],
                ['name' => 'SB. MENARA BARU', 'code' => 'MNRB', 'capacity' => 50, 'is_active' => true],
                ['name' => 'SB. MENARA NIKLAS', 'code' => 'MNRN', 'capacity' => 50, 'is_active' => true],
                ['name' => 'SB. ANDALAS 06', 'code' => 'ANDLS06', 'capacity' => 50, 'is_active' => true],
                ['name' => 'SB. MENARA PELITA', 'code' => 'MNRP', 'capacity' => 50, 'is_active' => true],
                ['name' => 'SB. TANJUNG MAS', 'code' => 'TJNGMS', 'capacity' => 50, 'is_active' => true],
            ];

            foreach ($speedboats as $speedboatData) {
                Speedboat::firstOrCreate(
                    ['code' => $speedboatData['code']],
                    $speedboatData
                );
                $this->command->info("   ✓ {$speedboatData['name']}");
            }

            $this->command->info('📍 Creating Destinations...');

            // Create common routes (based on WooCommerce orders)
            $destinations = [
                [
                    'code' => 'TJSL-TRK',
                    'name' => 'Tanjung Selor - Tarakan',
                    'departure_location' => 'TANJUNG SELOR',
                    'destination_location' => 'TARAKAN',
                    'adult_price' => 250000,
                    'child_price' => 200000,
                    'toddler_price' => 125000,
                    'is_active' => true
                ],
                [
                    'code' => 'TRK-TJSL',
                    'name' => 'Tarakan - Tanjung Selor',
                    'departure_location' => 'TARAKAN',
                    'destination_location' => 'TANJUNG SELOR',
                    'adult_price' => 250000,
                    'child_price' => 200000,
                    'toddler_price' => 125000,
                    'is_active' => true
                ],
                [
                    'code' => 'TJSL-NNK',
                    'name' => 'Tanjung Selor - Nunukan',
                    'departure_location' => 'TANJUNG SELOR',
                    'destination_location' => 'NUNUKAN',
                    'adult_price' => 300000,
                    'child_price' => 250000,
                    'toddler_price' => 150000,
                    'is_active' => true
                ],
                [
                    'code' => 'NNK-TJSL',
                    'name' => 'Nunukan - Tanjung Selor',
                    'departure_location' => 'NUNUKAN',
                    'destination_location' => 'TANJUNG SELOR',
                    'adult_price' => 300000,
                    'child_price' => 250000,
                    'toddler_price' => 150000,
                    'is_active' => true
                ],
            ];

            foreach ($destinations as $destData) {
                Destination::firstOrCreate(
                    ['code' => $destData['code']],
                    $destData
                );
                $this->command->info("   ✓ {$destData['departure_location']} → {$destData['destination_location']}");
            }

            $this->command->info('📅 Creating Schedules...');

            // Create schedules for each speedboat and destination
            $speedboatsCreated = Speedboat::all();
            $destinationsCreated = Destination::all();

            // Common departure times
            $departureTimes = ['06:00:00', '08:00:00', '10:00:00', '12:00:00', '14:00:00', '16:00:00'];

            foreach ($speedboatsCreated as $speedboat) {
                foreach ($destinationsCreated as $destination) {
                    // Create 2-3 schedules per route per speedboat
                    foreach (array_slice($departureTimes, 0, 3) as $time) {
                        $schedule = Schedule::firstOrCreate(
                            [
                                'speedboat_id' => $speedboat->id,
                                'destination_id' => $destination->id,
                                'departure_time' => $time
                            ],
                            [
                                'name' => $speedboat->name . ' - ' . substr($time, 0, 5),
                                'capacity' => $speedboat->capacity,
                                'is_active' => true
                            ]
                        );

                        if ($schedule->wasRecentlyCreated) {
                            $this->command->info("   ✓ {$speedboat->name} - {$destination->departure_location} → {$destination->destination_location} at {$time}");
                        }
                    }
                }
            }

            DB::commit();

            $this->command->info('');
            $this->command->info('✅ Master data seeded successfully!');
            $this->command->info('');
            $this->command->table(
                ['Entity', 'Count'],
                [
                    ['Speedboats', Speedboat::count()],
                    ['Destinations', Destination::count()],
                    ['Schedules', Schedule::count()],
                ]
            );

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('❌ Error seeding data: ' . $e->getMessage());
            throw $e;
        }
    }
}
