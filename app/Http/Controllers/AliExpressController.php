<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\AliExpressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AliExpressController extends Controller
{
    protected $aliExpressService;
    
    public function __construct(AliExpressService $aliExpressService)
    {
        $this->aliExpressService = $aliExpressService;
    }
    
    /**
     * Import product from AliExpress URL
     */
    public function importProduct(Request $request)
    {
        Log::info('AliExpress import request received', ['request_data' => $request->all()]);
        
        $request->validate([
            'aliexpress_url' => 'required|url',
            'category_id' => 'required|exists:categories,id'
        ]);
        
        try {
            // Fetch product details from AliExpress
            $productData = $this->aliExpressService->fetchProductDetails($request->aliexpress_url);
            
            // Create product in our system
            $product = new Product();
            $product->name = $productData['name'];
            $product->description = $productData['description'];
            $product->price = $productData['price'];
            $product->sku = 'AE-' . $productData['product_id'];
            $product->category_id = $request->category_id;
            $product->supplier_type = 'online';
            $product->supplier_link = $request->aliexpress_url;
            $product->is_active = true;
            $product->slug = Str::slug($productData['name']['en'] ?? 'aliexpress-product') . '-' . time();
            
            // Handle product images
            if (!empty($productData['images'])) {
                // Download and store images locally
                $imagePaths = $this->aliExpressService->downloadProductImages($productData['images'], $productData['product_id']);
                if (!empty($imagePaths)) {
                    // Set main image
                    $product->image = $imagePaths[0];
                    
                    // Set additional images
                    $product->images = array_slice($imagePaths, 1);
                }
            }
            
            // Set colors and sizes if available
            if (!empty($productData['attributes']['color'])) {
                $product->colors = [
                    'en' => $productData['attributes']['color'],
                    'ar' => $productData['attributes']['color'] // In a real implementation, you would translate these
                ];
            }
            
            if (!empty($productData['attributes']['size'])) {
                $product->sizes = [
                    'en' => $productData['attributes']['size'],
                    'ar' => $productData['attributes']['size'] // In a real implementation, you would translate these
                ];
            }
            
            $product->save();
            
            Log::info('AliExpress product imported successfully', ['product_id' => $product->id, 'sku' => $product->sku]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product imported successfully',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('AliExpress product import failed: ' . $e->getMessage(), [
                'url' => $request->aliexpress_url,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to import product: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Estimate shipping cost for a product based on customer location
     */
    public function estimateShipping(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'address_id' => 'required|exists:addresses,id'
        ]);
        
        try {
            $product = Product::findOrFail($request->product_id);
            $address = auth()->user()->addresses()->findOrFail($request->address_id);
            
            // Prepare customer location data
            $customerLocation = [
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'country' => $address->country,
                'city' => $address->city
            ];
            
            // Estimate shipping cost
            $shippingInfo = $this->aliExpressService->estimateShippingCost(
                $product->sku, 
                $customerLocation
            );
            
            return response()->json([
                'success' => true,
                'shipping_info' => $shippingInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to estimate shipping: ' . $e->getMessage()
            ], 500);
        }
    }
}