<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TemuService
{
    protected $apiUrl;
    protected $appKey;
    protected $secret;

    public function __construct()
    {
        $this->apiUrl = config('services.temu.url');
        $this->appKey = config('services.temu.key');
        $this->secret = config('services.temu.secret');
    }

    /**
     * Generate signature for API requests
     */
    protected function generateSignature($params, $timestamp)
    {
        // Sort parameters
        ksort($params);
        
        // Create signature string
        $signString = $this->secret . $timestamp;
        foreach ($params as $key => $value) {
            $signString .= $key . $value;
        }
        $signString .= $this->secret;
        
        // Return MD5 hash
        return md5($signString);
    }

    /**
     * Make authenticated API request
     */
    protected function makeRequest($endpoint, $params = [], $method = 'GET')
    {
        try {
            $timestamp = time();
            
            // Add common parameters
            $params['app_key'] = $this->appKey;
            $params['timestamp'] = $timestamp;
            
            // Generate signature
            $params['sign'] = $this->generateSignature($params, $timestamp);
            
            $url = $this->apiUrl . $endpoint;
            
            $response = Http::withOptions([
                'verify' => false, // For development only
            ])->asForm()->{$method}($url, $params);
            
            $result = $response->json();
            
            if (isset($result['error_response'])) {
                throw new Exception('Temu API Error: ' . ($result['error_response']['msg'] ?? 'Unknown error'));
            }
            
            return $result;
        } catch (Exception $e) {
            Log::error('Temu API request failed: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Fetch products from Temu
     */
    public function getProducts($page = 1, $size = 20)
    {
        try {
            $params = [
                'page_no' => $page,
                'page_size' => $size,
            ];
            
            return $this->makeRequest('/products', $params);
        } catch (Exception $e) {
            Log::error('Temu getProducts failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get product details from Temu
     */
    public function getProductDetail($id)
    {
        try {
            $params = [
                'product_id' => $id,
            ];
            
            return $this->makeRequest('/product/detail', $params);
        } catch (Exception $e) {
            Log::error('Temu getProductDetail failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sync stock for a product
     */
    public function syncStock($productId, $quantity)
    {
        try {
            $params = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
            
            return $this->makeRequest('/product/stock', $params, 'POST');
        } catch (Exception $e) {
            Log::error('Temu syncStock failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create order on Temu
     */
    public function createOrder($orderData)
    {
        try {
            return $this->makeRequest('/order/create', $orderData, 'POST');
        } catch (Exception $e) {
            Log::error('Temu createOrder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch tracking info for an order
     */
    public function getTracking($orderId)
    {
        try {
            $params = [
                'order_id' => $orderId,
            ];
            
            return $this->makeRequest('/order/tracking', $params);
        } catch (Exception $e) {
            Log::error('Temu getTracking failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            // Simple ping request to test connection
            $params = [
                'timestamp' => time(),
            ];
            
            $result = $this->makeRequest('/ping', $params);
            return isset($result['success']) && $result['success'];
        } catch (Exception $e) {
            Log::error('Temu testConnection failed: ' . $e->getMessage());
            return false;
        }
    }
}