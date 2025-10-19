<?php

namespace App\Services;

use App\Models\Speedboat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceProductService
{
    private $apiUrl;
    private $consumerKey;
    private $consumerSecret;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.woocommerce.url', env('WOOCOMMERCE_URL', 'https://naikspeed.com')), '/');
        $this->consumerKey = config('services.woocommerce.consumer_key');
        $this->consumerSecret = config('services.woocommerce.consumer_secret');
    }

    /**
     * Check if WooCommerce is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->consumerKey) && !empty($this->consumerSecret);
    }

    /**
     * Create a new product in WooCommerce for a speedboat
     */
    public function createProduct(Speedboat $speedboat): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WooCommerce not configured',
            ];
        }

        try {
            // Prepare product data
            $productData = [
                'name' => $speedboat->name,
                'type' => 'simple',
                'status' => $speedboat->is_active ? 'publish' : 'draft',
                'description' => $speedboat->description ?? 'Speedboat ' . $speedboat->name,
                'short_description' => 'Kode: ' . $speedboat->code . ' | Kapasitas: ' . $speedboat->capacity . ' orang',
                'sku' => $speedboat->code,
                'manage_stock' => false,
                'meta_data' => [
                    [
                        'key' => '_speedboat_code',
                        'value' => $speedboat->code,
                    ],
                    [
                        'key' => '_speedboat_capacity',
                        'value' => $speedboat->capacity,
                    ],
                    [
                        'key' => '_speedboat_type',
                        'value' => $speedboat->type ?? '',
                    ],
                ],
            ];

            // Create product via WooCommerce REST API
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->post("{$this->apiUrl}/wp-json/wc/v3/products", $productData);

            if ($response->successful()) {
                $product = $response->json();

                Log::info('WooCommerce product created', [
                    'speedboat_id' => $speedboat->id,
                    'product_id' => $product['id'],
                ]);

                return [
                    'success' => true,
                    'product_id' => $product['id'],
                    'message' => 'Product created successfully in WooCommerce',
                ];
            } else {
                $errorMessage = $response->json()['message'] ?? 'Unknown error';

                Log::error('Failed to create WooCommerce product', [
                    'speedboat_id' => $speedboat->id,
                    'status' => $response->status(),
                    'error' => $errorMessage,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to create product: ' . $errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception creating WooCommerce product', [
                'speedboat_id' => $speedboat->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing product in WooCommerce
     */
    public function updateProduct(Speedboat $speedboat): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WooCommerce not configured',
            ];
        }

        if (!$speedboat->woocommerce_product_id) {
            return [
                'success' => false,
                'message' => 'Speedboat not linked to WooCommerce product',
            ];
        }

        try {
            // Prepare product data
            $productData = [
                'name' => $speedboat->name,
                'status' => $speedboat->is_active ? 'publish' : 'draft',
                'description' => $speedboat->description ?? 'Speedboat ' . $speedboat->name,
                'short_description' => 'Kode: ' . $speedboat->code . ' | Kapasitas: ' . $speedboat->capacity . ' orang',
                'sku' => $speedboat->code,
                'meta_data' => [
                    [
                        'key' => '_speedboat_code',
                        'value' => $speedboat->code,
                    ],
                    [
                        'key' => '_speedboat_capacity',
                        'value' => $speedboat->capacity,
                    ],
                    [
                        'key' => '_speedboat_type',
                        'value' => $speedboat->type ?? '',
                    ],
                ],
            ];

            // Update product via WooCommerce REST API
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->put("{$this->apiUrl}/wp-json/wc/v3/products/{$speedboat->woocommerce_product_id}", $productData);

            if ($response->successful()) {
                Log::info('WooCommerce product updated', [
                    'speedboat_id' => $speedboat->id,
                    'product_id' => $speedboat->woocommerce_product_id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Product updated successfully in WooCommerce',
                ];
            } else {
                $errorMessage = $response->json()['message'] ?? 'Unknown error';

                Log::error('Failed to update WooCommerce product', [
                    'speedboat_id' => $speedboat->id,
                    'product_id' => $speedboat->woocommerce_product_id,
                    'status' => $response->status(),
                    'error' => $errorMessage,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to update product: ' . $errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception updating WooCommerce product', [
                'speedboat_id' => $speedboat->id,
                'product_id' => $speedboat->woocommerce_product_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create or update Bus in WooCommerce Bus Booking plugin
     * This assumes the Bus Booking plugin has REST API endpoints
     */
    public function createOrUpdateBus(Speedboat $speedboat): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'WooCommerce not configured',
            ];
        }

        try {
            $busData = [
                'title' => $speedboat->name,
                'status' => $speedboat->is_active ? 'publish' : 'draft',
                'seats' => $speedboat->capacity,
                'code' => $speedboat->code,
            ];

            // Note: This endpoint might vary depending on Bus Booking plugin version
            // You may need to adjust this based on the actual plugin API
            $endpoint = $speedboat->woocommerce_bus_id
                ? "/wp-json/wc/v3/bus-booking/buses/{$speedboat->woocommerce_bus_id}"
                : "/wp-json/wc/v3/bus-booking/buses";

            $method = $speedboat->woocommerce_bus_id ? 'put' : 'post';

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->$method("{$this->apiUrl}{$endpoint}", $busData);

            if ($response->successful()) {
                $bus = $response->json();

                Log::info('WooCommerce bus created/updated', [
                    'speedboat_id' => $speedboat->id,
                    'bus_id' => $bus['id'] ?? $speedboat->woocommerce_bus_id,
                ]);

                return [
                    'success' => true,
                    'bus_id' => $bus['id'] ?? $speedboat->woocommerce_bus_id,
                    'message' => 'Bus created/updated successfully in WooCommerce',
                ];
            } else {
                // Bus Booking plugin might not have REST API, that's okay
                Log::warning('Bus Booking API not available or failed', [
                    'speedboat_id' => $speedboat->id,
                    'status' => $response->status(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Bus Booking API not available. Please set Bus ID manually.',
                    'skippable' => true, // This error is not critical
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Exception with Bus Booking API', [
                'speedboat_id' => $speedboat->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Bus Booking API error: ' . $e->getMessage(),
                'skippable' => true, // This error is not critical
            ];
        }
    }

    /**
     * Get product details from WooCommerce
     */
    public function getProduct(int $productId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->get("{$this->apiUrl}/wp-json/wc/v3/products/{$productId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get WooCommerce product', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
