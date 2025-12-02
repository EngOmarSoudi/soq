<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\API\TemuService;
use App\Services\API\CJService;
use App\Services\API\SheinService;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncStockPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:stock-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync stock and price information from external platforms';

    /**
     * Execute the console command.
     */
    public function handle(TemuService $temuService, CJService $cjService, SheinService $sheinService)
    {
        try {
            $this->info('Starting stock and price sync...');
            
            // Get all online products
            $products = Product::where('supplier_type', 'online')->get();
            
            $syncedCount = 0;
            $errorCount = 0;
            
            $this->info("Found {$products->count()} online products to sync...");
            
            foreach ($products as $product) {
                try {
                    // Determine platform based on SKU prefix
                    if (strpos($product->sku, 'TEMU-') === 0) {
                        // Sync Temu product
                        $productId = str_replace('TEMU-', '', $product->sku);
                        $this->syncTemuProduct($temuService, $product, $productId);
                    } elseif (strpos($product->sku, 'CJ-') === 0) {
                        // Sync CJ product
                        $productId = str_replace('CJ-', '', $product->sku);
                        $this->syncCJProduct($cjService, $product, $productId);
                    } elseif (strpos($product->sku, 'SHEIN-') === 0) {
                        // Sync Shein product
                        $productId = str_replace('SHEIN-', '', $product->sku);
                        $this->syncSheinProduct($sheinService, $product, $productId);
                    }
                    
                    $syncedCount++;
                    
                } catch (Exception $e) {
                    $errorCount++;
                    Log::error('Failed to sync product: ' . $e->getMessage(), [
                        'product_sku' => $product->sku,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $this->error("Failed to sync product {$product->sku}: " . $e->getMessage());
                }
            }
            
            $this->info("Stock and price sync completed. Synced: {$syncedCount}, Errors: {$errorCount}");
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            Log::error('Stock and price sync failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Stock and price sync failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Sync Temu product stock and price
     */
    protected function syncTemuProduct($temuService, $product, $productId)
    {
        try {
            // Get product details
            $response = $temuService->getProductDetail($productId);
            
            if (isset($response['data'])) {
                $detail = $response['data'];
                
                // Update stock and price
                $product->stock_quantity = $detail['stock'] ?? $product->stock_quantity;
                $product->price = $detail['price'] ?? $product->price;
                
                // Mark as inactive if out of stock
                if (($detail['stock'] ?? 0) <= 0) {
                    $product->is_active = false;
                }
                
                $product->save();
                
                $this->info("Synced Temu product: {$product->sku}");
            }
        } catch (Exception $e) {
            throw new Exception("Temu sync failed: " . $e->getMessage());
        }
    }
    
    /**
     * Sync CJ product stock and price
     */
    protected function syncCJProduct($cjService, $product, $productId)
    {
        try {
            // Get product details
            $response = $cjService->getProductDetails($productId);
            
            if (isset($response['data'])) {
                $detail = $response['data'];
                
                // Update stock and price
                $product->stock_quantity = $detail['availableStock'] ?? $product->stock_quantity;
                $product->price = $detail['price'] ?? $product->price;
                
                // Mark as inactive if out of stock
                if (($detail['availableStock'] ?? 0) <= 0) {
                    $product->is_active = false;
                }
                
                $product->save();
                
                $this->info("Synced CJ product: {$product->sku}");
            }
        } catch (Exception $e) {
            throw new Exception("CJ sync failed: " . $e->getMessage());
        }
    }
    
    /**
     * Sync Shein product stock and price
     */
    protected function syncSheinProduct($sheinService, $product, $productId)
    {
        // Shein API doesn't have a direct product detail endpoint in our implementation
        // In a real implementation, you would call the appropriate Shein API endpoint
        $this->info("Shein sync not implemented for product: {$product->sku}");
    }
}