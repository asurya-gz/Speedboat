<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceService
{
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.woocommerce.base_url');
        $this->consumerKey = config('services.woocommerce.consumer_key');
        $this->consumerSecret = config('services.woocommerce.consumer_secret');
    }

    /**
     * Make authenticated request to WooCommerce API
     */
    protected function request($method, $endpoint, $data = [])
    {
        try {
            $url = $this->baseUrl . $endpoint;

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->{$method}($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('WooCommerce API Error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('WooCommerce API Exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fetch recent orders from WooCommerce
     */
    public function getOrders($params = [])
    {
        $defaultParams = [
            'orderby' => 'date',
            'order' => 'desc',
            'per_page' => 20
        ];

        $queryParams = array_merge($defaultParams, $params);
        $queryString = http_build_query($queryParams);

        return $this->request('get', "/orders?{$queryString}");
    }

    /**
     * Get specific order by ID
     */
    public function getOrder($orderId)
    {
        return $this->request('get', "/orders/{$orderId}");
    }

    /**
     * Create new order in WooCommerce
     */
    public function createOrder($orderData)
    {
        return $this->request('post', '/orders', $orderData);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status)
    {
        return $this->request('put', "/orders/{$orderId}", [
            'status' => $status
        ]);
    }

    /**
     * Get products (speedboat tickets)
     */
    public function getProducts($params = [])
    {
        $defaultParams = [
            'per_page' => 100,
            'status' => 'publish'
        ];

        $queryParams = array_merge($defaultParams, $params);
        $queryString = http_build_query($queryParams);

        return $this->request('get', "/products?{$queryString}");
    }

    /**
     * Get product by ID
     */
    public function getProduct($productId)
    {
        return $this->request('get', "/products/{$productId}");
    }

    /**
     * Parse WooCommerce order to local transaction format
     */
    public function parseOrderToTransaction($order)
    {
        try {
            // Extract line item (assuming first item is the ticket)
            $lineItem = $order['line_items'][0] ?? null;
            if (!$lineItem) {
                throw new \Exception('No line items in order');
            }

            // Extract metadata
            $metaData = collect($lineItem['meta_data']);

            // Parse boarding location and time
            $boardingValue = $this->getMetaValue($metaData, 'Boarding')
                          ?? $this->getMetaValue($metaData, '_wbtm_bp');
            $droppingValue = $this->getMetaValue($metaData, 'Dropping')
                          ?? $this->getMetaValue($metaData, '_wbtm_dp');
            $boardingTime = $this->getMetaValue($metaData, '_wbtm_bp_time');
            $droppingTime = $this->getMetaValue($metaData, '_wbtm_dp_time');

            // Parse boarding string: "TANJUNG SELOR, Pelabuhan Kayan II(19/10/2025  06:50)"
            if ($boardingValue && preg_match('/^(.*?)\((\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2})\)/', $boardingValue, $matches)) {
                $departureLocation = trim($matches[1]);
                $departureDate = \Carbon\Carbon::createFromFormat('d/m/Y', $matches[2])->format('Y-m-d');
                $departureTime = $matches[3];
            } else {
                // Fallback to separate fields
                $departureLocation = $boardingValue;
                $departureDate = $boardingTime ? \Carbon\Carbon::parse($boardingTime)->format('Y-m-d') : null;
                $departureTime = $boardingTime ? \Carbon\Carbon::parse($boardingTime)->format('H:i') : null;
            }

            // Extract only city name (before comma)
            if (strpos($departureLocation, ',') !== false) {
                $departureLocation = trim(explode(',', $departureLocation)[0]);
            }

            if ($droppingValue && preg_match('/^(.*?)\(/', $droppingValue, $matches)) {
                $destinationLocation = trim($matches[1]);
            } else {
                $destinationLocation = $droppingValue;
            }

            // Extract only city name (before comma)
            if (strpos($destinationLocation, ',') !== false) {
                $destinationLocation = trim(explode(',', $destinationLocation)[0]);
            }

            // Get ticket info
            $ticketInfo = $this->getMetaValue($metaData, '_wbtm_ticket_info');
            if (is_string($ticketInfo)) {
                $ticketInfo = json_decode($ticketInfo, true);
            }
            $ticketInfo = is_array($ticketInfo) ? $ticketInfo[0] : [];

            // Get seat passenger map
            $seatPassengerMap = $this->getMetaValue($metaData, 'seat_passenger_map');
            if (is_string($seatPassengerMap)) {
                $seatPassengerMap = json_decode($seatPassengerMap, true);
            }

            // Get passenger name (from billing or seat map)
            $passengerName = $order['billing']['first_name'] ?? '';
            if (empty($passengerName) && !empty($seatPassengerMap)) {
                $passengerName = reset($seatPassengerMap); // Get first passenger
            }

            // Get bus/speedboat ID
            $busId = $this->getMetaValue($metaData, '_wbtm_bus_id');

            return [
                'woocommerce_order_id' => $order['id'],
                'speedboat_name' => $lineItem['name'],
                'woocommerce_bus_id' => $busId,
                'departure_location' => $departureLocation,
                'destination_location' => $destinationLocation,
                'departure_date' => $departureDate,
                'departure_time' => $departureTime,
                'passenger_name' => $passengerName,
                'passenger_phone' => $order['billing']['phone'] ?? '',
                'passenger_email' => $order['billing']['email'] ?? '',
                'adult_count' => $ticketInfo['ticket_qty'] ?? 1,
                'child_count' => 0,
                'toddler_count' => 0,
                'total_amount' => $order['total'],
                'payment_method' => $order['payment_method'],
                'payment_status' => $order['status'] === 'completed' ? 'paid' : 'pending',
                'paid_at' => $order['date_paid'] ? \Carbon\Carbon::parse($order['date_paid']) : null,
                'created_at' => \Carbon\Carbon::parse($order['date_created']),
                'seat_passenger_map' => $seatPassengerMap,
                'line_item_id' => $lineItem['id'],
                'ticket_price' => $ticketInfo['ticket_price'] ?? $lineItem['total'],
                'passenger_type' => $ticketInfo['ticket_type'] ?? 'adult'
            ];

        } catch (\Exception $e) {
            Log::error('Error parsing WooCommerce order', [
                'order_id' => $order['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Convert local transaction to WooCommerce order format
     */
    public function formatTransactionForWooCommerce($transaction)
    {
        $schedule = $transaction->schedule;
        $destination = $schedule->destination;
        $speedboat = $schedule->speedboat;
        $tickets = $transaction->tickets;

        // Build seat passenger map
        $seatPassengerMap = [];
        foreach ($tickets as $index => $ticket) {
            $seatPassengerMap[$index + 1] = $ticket->passenger_name;
        }

        // Build ticket info
        $ticketInfo = [];
        $departureTimeFormatted = \Carbon\Carbon::parse($schedule->departure_time)->format('H:i');
        foreach ($tickets as $ticket) {
            $ticketInfo[] = [
                'ticket_name' => $ticket->passenger_type === 'adult' ? 'Dewasa' : 'Balita',
                'seat_name' => $ticket->seat_number,
                'ticket_type' => $ticket->passenger_type === 'adult' ? '0' : '2',
                'ticket_price' => (string) $ticket->price,
                'ticket_qty' => '1',
                'date' => $transaction->departure_date->format('Y-m-d') . ' ' . $departureTimeFormatted
            ];
        }

        // Find WooCommerce product ID for this speedboat
        $productId = $speedboat->woocommerce_product_id;
        if (!$productId) {
            throw new \Exception("Speedboat {$speedboat->name} does not have WooCommerce product mapping");
        }

        $departureTimeStr = \Carbon\Carbon::parse($schedule->departure_time)->format('H:i');
        $arrivalTimeStr = \Carbon\Carbon::parse($schedule->departure_time)->addMinutes(100)->format('H:i');

        $boardingString = "{$destination->departure_location}({$transaction->departure_date->format('d/m/Y')}  {$departureTimeStr})";
        $droppingString = "{$destination->destination_location}({$transaction->departure_date->format('d/m/Y')}  {$arrivalTimeStr})";

        return [
            'payment_method' => $transaction->payment_method,
            'payment_method_title' => ucfirst($transaction->payment_method),
            'set_paid' => $transaction->payment_status === 'paid',
            'status' => $transaction->payment_status === 'paid' ? 'completed' : 'pending',
            'billing' => [
                'first_name' => $transaction->passenger_name,
                'phone' => $transaction->notes ?? '081234567890',
                'email' => 'noreply@naikspeed.com', // Default email for POS transactions
                'country' => 'ID'
            ],
            'line_items' => [
                [
                    'product_id' => $productId,
                    'quantity' => $transaction->adult_count + $transaction->child_count + $transaction->toddler_count,
                    'meta_data' => [
                        ['key' => 'Boarding', 'value' => $boardingString],
                        ['key' => 'Dropping', 'value' => $droppingString],
                        ['key' => 'Seat Type', 'value' => 'Dewasa'],
                        ['key' => '_bus_id', 'value' => (string) ($speedboat->woocommerce_bus_id ?? '')],
                        ['key' => '_wbtm_bus_id', 'value' => (string) ($speedboat->woocommerce_bus_id ?? '')],
                        ['key' => '_wbtm_ticket_info', 'value' => $ticketInfo],
                        ['key' => '_wbtm_bp', 'value' => $destination->departure_location],
                        ['key' => '_wbtm_bp_time', 'value' => $transaction->departure_date->format('Y-m-d') . ' ' . $departureTimeStr],
                        ['key' => '_wbtm_dp', 'value' => $destination->destination_location],
                        ['key' => '_wbtm_base_price', 'value' => (string) $destination->adult_price],
                        ['key' => '_wbtm_qty', 'value' => (string) ($transaction->adult_count + $transaction->toddler_count)],
                        ['key' => 'seat_passenger_map', 'value' => $seatPassengerMap],
                    ]
                ]
            ],
            'meta_data' => [
                ['key' => 'pos_transaction_code', 'value' => $transaction->transaction_code],
                ['key' => 'pos_transaction_id', 'value' => (string) $transaction->id],
            ]
        ];
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
     * Check if WooCommerce API is reachable
     */
    public function checkConnection()
    {
        try {
            $response = $this->request('get', '/system_status');
            return $response['success'];
        } catch (\Exception $e) {
            return false;
        }
    }
}
