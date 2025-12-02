<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\API\TemuService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportTemuProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:temu-products {--page=1 : Page number to import} {--limit=20 : Number of products per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Temu API';

    /**
     * Execute the console command.
     */
    public function handle(TemuService $temuService)
    {
        try {
            $this->info('Starting Temu product import...');
            
            $page = $this->option('page');
            $limit = $this->option('limit');
            
            // Fetch products from Temu
            $response = $temuService->getProducts($page, $limit);
            
            if (!isset($response['data']['products'])) {
                $this->error('No products found in Temu API response');
                return Command::FAILURE;
            }
            
            $products = $response['data']['products'];
            $importedCount = 0;
            
            $this->info("Found {$response['data']['total']} products. Importing page {$page}...");
            
            foreach ($products as $productData) {
                try {
                    // Get detailed product information
                    $detailResponse = $temuService->getProductDetail($productData['product_id']);
                    
                    if (!isset($detailResponse['data'])) {
                        $this->warn("Failed to get details for product {$productData['product_id']}");
                        continue;
                    }
                    
                    $detail = $detailResponse['data'];
                    
                    // Find or create category
                    $categoryName = isset($detail['category_name']) ? $detail['category_name'] : 'Uncategorized';
                    $category = Category::firstOrCreate(
                        ['slug' => str_slug($categoryName)],
                        [
                            'name' => ['en' => $categoryName, 'ar' => $categoryName],
                            'description' => ['en' => '', 'ar' => ''],
                            'is_active' => true,
                        ]
                    );
                    
                    // Prepare product data
                    $productName = isset($detail['product_name']) ? $detail['product_name'] : 'Unknown Product';
                    $productDescription = isset($detail['description']) ? $detail['description'] : '';
                    $productPrice = isset($detail['price']) ? $detail['price'] : 0;
                    $productUrl = isset($detail['product_url']) ? $detail['product_url'] : '';
                    $productStock = isset($detail['stock']) ? $detail['stock'] : 0;
                    
                    $productAttributes = [
                        'name' => [
                            'en' => $productName,
                            'ar' => $productName,
                        ],
                        'description' => [
                            'en' => $productDescription,
                            'ar' => $productDescription,
                        ],
                        'sku' => 'TEMU-' . $detail['product_id'],
                        'price' => $productPrice,
                        'category_id' => $category->id,
                        'supplier_type' => 'online',
                        'supplier_link' => $productUrl,
                        'is_active' => true,
                        'is_featured' => false,
                        'stock_quantity' => $productStock,
                    ];
                    
                    // Handle images
                    if (!empty($detail['images'])) {
                        $productAttributes['image'] = $detail['images'][0] ?? null;
                        $productAttributes['images'] = array_slice($detail['images'], 1) ?? [];
                    }
                    
                    // Handle variants
                    if (!empty($detail['variants'])) {
                        $colors = [];
                        $sizes = [];
                        
                        foreach ($detail['variants'] as $variant) {
                            if (!empty($variant['color'])) {
                                $colors[] = $variant['color'];
                            }
                            if (!empty($variant['size'])) {
                                $sizes[] = $variant['size'];
                            }
                        }
                        
                        if (!empty($colors)) {
                            $productAttributes['colors'] = [
                                'en' => array_unique($colors),
                                'ar' => array_unique($colors),
                            ];
                        }
                        
                        if (!empty($sizes)) {
                            $productAttributes['sizes'] = [
                                'en' => array_unique($sizes),
                                'ar' => array_unique($sizes),
                            ];
                        }
                    }
                    
                    // Create or update product
                    $product = Product::updateOrCreate(
                        ['sku' => 'TEMU-' . $detail['product_id']],
                        $productAttributes
                    );
                    
                    $importedCount++;
                    $this->info("Imported product: {$product->name['en']}");
                    
                } catch (Exception $e) {
                    $productId = isset($productData['product_id']) ? $productData['product_id'] : 'unknown';
                    Log::error('Failed to import Temu product: ' . $e->getMessage(), [
                        'product_id' => $productId,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $this->error("Failed to import product {$productId}: " . $e->getMessage());
                }
            }
            
            $this->info("Successfully imported {$importedCount} products from Temu");
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            Log::error('Temu product import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Temu product import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}