<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CJService
{
    protected $apiUrl;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->apiUrl = config('services.cj.url');
        $this->apiKey = config('services.cj.key');
        $this->apiSecret = config('services.cj.secret');
    }

    /**
     * Make authenticated API request
     */
    protected function makeRequest($endpoint, $params = [], $method = 'GET')
    {
        try {
            $headers = [
                'CJ-Access-Token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ];
            
            $url = $this->apiUrl . $endpoint;
            
            if ($method === 'GET') {
                $response = Http::withOptions([
                    'verify' => false, // For development only
                ])->withHeaders($headers)->get($url, $params);
            } else {
                $response = Http::withOptions([
                    'verify' => false, // For development only
                ])->withHeaders($headers)->{$method}($url, $params);
            }
            
            $result = $response->json();
            
            if (isset($result['error']) && $result['error']) {
                throw new Exception('CJ API Error: ' . ($result['message'] ?? 'Unknown error'));
            }
            
            return $result;
        } catch (Exception $e) {
            Log::error('CJ API request failed: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'params' => $params,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Search products on CJ
     */
    public function searchProducts($keyword, $page = 1, $size = 20)
    {
        try {
            $params = [
                'keywords' => $keyword,
                'page' => $page,
                'size' => $size,
            ];
            
            return $this->makeRequest('/products/search', $params);
        } catch (Exception $e) {
            Log::error('CJ searchProducts failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get product details from CJ
     */
    public function getProductDetails($productId)
    {
        try {
            $params = [
                'pid' => $productId,
            ];
            
            return $this->makeRequest('/products/get', $params);
        } catch (Exception $e) {
            Log::error('CJ getProductDetails failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create order on CJ
     */
    public function createOrder($orderData)
    {
        try {
            return $this->makeRequest('/orders/create', $orderData, 'POST');
        } catch (Exception $e) {
            Log::error('CJ createOrder failed: ' . $e->getMessage());
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
                'orderId' => $orderId,
            ];
            
            return $this->makeRequest('/orders/tracking', $params);
        } catch (Exception $e) {
            Log::error('CJ getTracking failed: ' . $e->getMessage());
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
            $result = $this->makeRequest('/ping');
            return isset($result['success']) && $result['success'];
        } catch (Exception $e) {
            Log::error('CJ testConnection failed: ' . $e->getMessage());
            return false;
        }
    }
}