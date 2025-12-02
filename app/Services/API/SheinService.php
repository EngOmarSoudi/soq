<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SheinService
{
    protected $apiUrl;
    protected $appId;
    protected $secret;

    public function __construct()
    {
        $this->apiUrl = config('services.shein.url');
        $this->appId = config('services.shein.id');
        $this->secret = config('services.shein.secret');
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
        
        // Return SHA256 hash
        return hash('sha256', $signString);
    }

    /**
     * Make authenticated API request
     */
    protected function makeRequest($endpoint, $params = [], $method = 'GET')
    {
        try {
            $timestamp = time();
            
            // Add common parameters
            $params['app_id'] = $this->appId;
            $params['timestamp'] = $timestamp;
            
            // Generate signature
            $params['sign'] = $this->generateSignature($params, $timestamp);
            
            $url = $this->apiUrl . $endpoint;
            
            $response = Http::withOptions([
                'verify' => false, // For development only
            ])->asForm()->{$method}($url, $params);
            
            $result = $response->json();
            
            if (isset($result['error_response'])) {
                throw new Exception('Shein API Error: ' . ($result['error_response']['msg'] ?? 'Unknown error'));
            }
            
            return $result;
        } catch (Exception $e) {
            Log::error('Shein API request failed: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Fetch products from Shein
     */
    public function getProducts($categoryId = null, $page = 1, $size = 20)
    {
        try {
            $params = [
                'page' => $page,
                'limit' => $size,
            ];
            
            if ($categoryId) {
                $params['category_id'] = $categoryId;
            }
            
            return $this->makeRequest('/products/list', $params);
        } catch (Exception $e) {
            Log::error('Shein getProducts failed: ' . $e->getMessage());
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
            Log::error('Shein getTracking failed: ' . $e->getMessage());
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
            Log::error('Shein testConnection failed: ' . $e->getMessage());
            return false;
        }
    }
}