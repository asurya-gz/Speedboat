<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WooCommerceService;
use App\Models\Speedboat;
use App\Models\Destination;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncMasterDataFromWooCommerce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync-master-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync master data (speedboats, destinations, schedules) from WooCommerce products';

    protected $woocommerce;

    public function __construct(WooCommerceService $woocommerce)
    {
        parent::__construct();
        $this->woocommerce = $woocommerce;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing master data from WooCommerce...');

        // Check connection
        if (!$this->woocommerce->checkConnection()) {
            $this->error('❌ Cannot connect to WooCommerce API');
            return 1;
        }

        $this->info('✅ Connection to WooCommerce established');

        try {
            // Fetch ALL products from WooCommerce (paginated)
            $this->info('📥 Fetching products from WooCommerce...');
            $allProducts = [];
            $page = 1;
            $perPage = 100;

            do {
                $response = $this->woocommerce->getProducts(['per_page' => $perPage, 'page' => $page]);

                if (!$response['success']) {
                    $this->error('❌ Failed to fetch products: ' . $response['error']);
                    return 1;
                }

                $products = $response['data'];
                $allProducts = array_merge($allProducts, $products);
                $this->line("Fetched page {$page}: " . count($products) . " products");

                $page++;
            } while (count($products) == $perPage); // Continue if we got a full page

            $products = $allProducts;
            $this->info('Total products found: ' . count($products));

            $speedboatsCreated = 0;
            $destinationsCreated = 0;
            $schedulesCreated = 0;
            $uniqueSpeedboats = [];

            // Parse products and extract unique speedboat names
            foreach ($products as $product) {
                $this->line("Processing product: {$product['name']} (ID: {$product['id']})");

                // Extract speedboat info from product name
                // Assuming product name format like: "SB. KALIMANTAN - Tanjung Selor to Tarakan"
                if (preg_match('/^(SB\.\s+[^-]+)/', $product['name'], $matches)) {
                    $speedboatName = trim($matches[1]);

                    if (!isset($uniqueSpeedboats[$speedboatName])) {
                        $uniqueSpeedboats[$speedboatName] = $product['id'];
                    }
                }
            }

            // Also fetch from recent orders to get all speedboat names
            $this->info('📥 Fetching recent orders to extract speedboat names...');
            $ordersResponse = $this->woocommerce->getOrders(['per_page' => 50, 'orderby' => 'date', 'order' => 'desc']);

            if ($ordersResponse['success']) {
                $orders = $ordersResponse['data'];
                foreach ($orders as $order) {
                    if (!empty($order['line_items'])) {
                        foreach ($order['line_items'] as $item) {
                            if (preg_match('/^(SB\.\s+[^-]+)/', $item['name'], $matches)) {
                                $speedboatName = trim($matches[1]);
                                if (!isset($uniqueSpeedboats[$speedboatName])) {
                                    $uniqueSpeedboats[$speedboatName] = null;
                                }
                            }
                        }
                    }
                }
            }

            $this->info('Found ' . count($uniqueSpeedboats) . ' unique speedboats');

            // Create speedboats
            foreach ($uniqueSpeedboats as $speedboatName => $productId) {
                $speedboat = Speedboat::firstOrCreate(
                    ['name' => $speedboatName],
                    [
                        'code' => strtoupper(preg_replace('/[^A-Z0-9]/', '', str_replace('SB.', '', $speedboatName))),
                        'capacity' => 50, // Default capacity
                        'woocommerce_product_id' => $productId,
                        'is_active' => true
                    ]
                );

                if ($speedboat->wasRecentlyCreated) {
                    $speedboatsCreated++;
                    $this->info("   ✓ Created speedboat: {$speedboatName}");
                } else {
                    // Update woocommerce_product_id if not set and we have one
                    if (!$speedboat->woocommerce_product_id && $productId) {
                        $speedboat->update(['woocommerce_product_id' => $productId]);
                    }
                }
            }

            // Parse destinations from WooCommerce orders (dynamic, not hardcoded)
            $this->info('📍 Parsing destinations from orders...');
            $destinationsData = [];

            if ($ordersResponse['success']) {
                $orders = $ordersResponse['data'];
                foreach ($orders as $order) {
                    if (!empty($order['line_items'])) {
                        foreach ($order['line_items'] as $item) {
                            $metaData = collect($item['meta_data']);

                            // Get boarding and dropping points
                            $boarding = $this->getMetaValue($metaData, '_wbtm_bp');
                            $dropping = $this->getMetaValue($metaData, '_wbtm_dp');

                            if ($boarding && $dropping) {
                                // Extract city names (before comma)
                                $departureLocation = $this->extractCityName($boarding);
                                $destinationLocation = $this->extractCityName($dropping);

                                if ($departureLocation && $destinationLocation) {
                                    $routeKey = $departureLocation . '-' . $destinationLocation;

                                    if (!isset($destinationsData[$routeKey])) {
                                        // Get price from ticket info
                                        $ticketInfo = $this->getMetaValue($metaData, '_wbtm_ticket_info');
                                        if (is_string($ticketInfo)) {
                                            $ticketInfo = json_decode($ticketInfo, true);
                                        }
                                        $ticketInfo = is_array($ticketInfo) ? $ticketInfo[0] : [];
                                        $price = isset($ticketInfo['ticket_price']) ? (int)$ticketInfo['ticket_price'] : 250000;

                                        $destinationsData[$routeKey] = [
                                            'departure' => strtoupper($departureLocation),
                                            'destination' => strtoupper($destinationLocation),
                                            'price' => $price
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $this->info('Found ' . count($destinationsData) . ' unique routes');

            // Create destinations from parsed data
            foreach ($destinationsData as $routeKey => $destData) {
                $code = strtoupper(substr($destData['departure'], 0, 4)) . '-' . strtoupper(substr($destData['destination'], 0, 3));

                $destination = Destination::firstOrCreate(
                    ['code' => $code],
                    [
                        'name' => $destData['departure'] . ' - ' . $destData['destination'],
                        'departure_location' => $destData['departure'],
                        'destination_location' => $destData['destination'],
                        'adult_price' => $destData['price'],
                        'child_price' => $destData['price'] * 0.8,
                        'toddler_price' => $destData['price'] * 0.5,
                        'is_active' => true
                    ]
                );

                if ($destination->wasRecentlyCreated) {
                    $destinationsCreated++;
                    $this->info("   ✓ Created destination: {$destData['departure']} → {$destData['destination']}");
                }
            }

            // Parse schedules from WooCommerce orders (dynamic, not auto-generated)
            $this->info('📅 Parsing schedules from orders...');
            $schedulesData = [];

            if ($ordersResponse['success']) {
                $orders = $ordersResponse['data'];
                foreach ($orders as $order) {
                    if (!empty($order['line_items'])) {
                        foreach ($order['line_items'] as $item) {
                            $metaData = collect($item['meta_data']);

                            // Get speedboat name
                            $speedboatName = null;
                            if (preg_match('/^(SB\.\s+[^-]+)/', $item['name'], $matches)) {
                                $speedboatName = trim($matches[1]);
                            }

                            // Get boarding/dropping locations
                            $boarding = $this->getMetaValue($metaData, '_wbtm_bp');
                            $dropping = $this->getMetaValue($metaData, '_wbtm_dp');
                            $boardingTime = $this->getMetaValue($metaData, '_wbtm_bp_time');

                            if ($speedboatName && $boarding && $dropping && $boardingTime) {
                                $departureLocation = $this->extractCityName($boarding);
                                $destinationLocation = $this->extractCityName($dropping);
                                $departureTime = \Carbon\Carbon::parse($boardingTime)->format('H:i:s');

                                $scheduleKey = $speedboatName . '|' . $departureLocation . '|' . $destinationLocation . '|' . $departureTime;

                                if (!isset($schedulesData[$scheduleKey])) {
                                    $schedulesData[$scheduleKey] = [
                                        'speedboat_name' => $speedboatName,
                                        'departure_location' => strtoupper($departureLocation),
                                        'destination_location' => strtoupper($destinationLocation),
                                        'departure_time' => $departureTime
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            $this->info('Found ' . count($schedulesData) . ' unique schedules');

            // Create schedules from parsed data
            foreach ($schedulesData as $scheduleData) {
                // Find speedboat
                $speedboat = Speedboat::where('name', $scheduleData['speedboat_name'])->first();
                if (!$speedboat) continue;

                // Find destination
                $destination = Destination::where('departure_location', $scheduleData['departure_location'])
                    ->where('destination_location', $scheduleData['destination_location'])
                    ->first();
                if (!$destination) continue;

                // Create or update schedule
                $schedule = Schedule::firstOrCreate(
                    [
                        'speedboat_id' => $speedboat->id,
                        'destination_id' => $destination->id,
                        'departure_time' => $scheduleData['departure_time']
                    ],
                    [
                        'name' => $speedboat->name . ' - ' . substr($scheduleData['departure_time'], 0, 5),
                        'capacity' => $speedboat->capacity,
                        'is_active' => true
                    ]
                );

                if ($schedule->wasRecentlyCreated) {
                    $schedulesCreated++;
                }
            }

            $this->newLine();
            $this->info('✅ Master data sync completed!');
            $this->newLine();
            $this->table(
                ['Entity', 'Created', 'Total'],
                [
                    ['Speedboats', $speedboatsCreated, Speedboat::count()],
                    ['Destinations', $destinationsCreated, Destination::count()],
                    ['Schedules', $schedulesCreated, Schedule::count()],
                ]
            );

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('Sync master data from WooCommerce failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Helper to get meta value from collection
     */
    protected function getMetaValue($metaDataCollection, $key)
    {
        $item = $metaDataCollection->firstWhere('key', $key);
        return $item['value'] ?? $item['display_value'] ?? null;
    }

    /**
     * Extract city name from location string
     * Example: "TANJUNG SELOR, Pelabuhan Kayan II" → "TANJUNG SELOR"
     */
    protected function extractCityName($locationString)
    {
        if (strpos($locationString, ',') !== false) {
            return trim(explode(',', $locationString)[0]);
        }
        return trim($locationString);
    }
}
