<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\API\SheinService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportSheinProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:shein-products {category?} {--page=1 : Page number to import} {--limit=20 : Number of products per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Shein API';

    /**
     * Execute the console command.
     */
    public function handle(SheinService $sheinService)
    {
        try {
            $this->info('Starting Shein product import...');
            
            $categoryId = $this->argument('category');
            $page = $this->option('page');
            $limit = $this->option('limit');
            
            // Fetch products from Shein
            $response = $sheinService->getProducts($categoryId, $page, $limit);
            
            if (!isset($response['data']['products'])) {
                $this->error('No products found in Shein API response');
                return Command::FAILURE;
            }
            
            $products = $response['data']['products'];
            $importedCount = 0;
            
            $this->info("Found " . count($products) . " products. Importing page {$page}...");
            
            foreach ($products as $productData) {
                try {
                    // Prepare product data
                    $productName = isset($productData['title']) ? $productData['title'] : 'Unknown Product';
                    $productDescription = isset($productData['description']) ? $productData['description'] : '';
                    $productPrice = isset($productData['shop_price']) ? $productData['shop_price'] : 0;
                    $productUrl = isset($productData['url']) ? $productData['url'] : '';
                    $productStock = isset($productData['stock']) ? $productData['stock'] : 0;
                    $productSku = isset($productData['goods_sn']) ? $productData['goods_sn'] : 'unknown';
                    
                    $productAttributes = [
                        'name' => [
                            'en' => $productName,
                            'ar' => $productName,
                        ],
                        'description' => [
                            'en' => $productDescription,
                            'ar' => $productDescription,
                        ],
                        'sku' => 'SHEIN-' . $productSku,
                        'price' => $productPrice,
                        'category_id' => 1, // Default to first category
                        'supplier_type' => 'online',
                        'supplier_link' => $productUrl,
                        'is_active' => true,
                        'is_featured' => false,
                        'stock_quantity' => $productStock,
                    ];
                    
                    // Handle images
                    if (!empty($productData['image'])) {
                        $productAttributes['image'] = $productData['image'];
                    }
                    
                    if (!empty($productData['images'])) {
                        $productAttributes['images'] = $productData['images'];
                    }
                    
                    // Handle variants
                    if (!empty($productData['colorList'])) {
                        $colors = [];
                        foreach ($productData['colorList'] as $color) {
                            $colorName = isset($color['color_name']) ? $color['color_name'] : (isset($color['color_value']) ? $color['color_value'] : '');
                            if (!empty($colorName)) {
                                $colors[] = $colorName;
                            }
                        }
                        
                        if (!empty($colors)) {
                            $productAttributes['colors'] = [
                                'en' => array_unique(array_filter($colors)),
                                'ar' => array_unique(array_filter($colors)),
                            ];
                        }
                    }
                    
                    if (!empty($productData['sizeList'])) {
                        $sizes = [];
                        foreach ($productData['sizeList'] as $size) {
                            $sizeName = isset($size['size_name']) ? $size['size_name'] : '';
                            if (!empty($sizeName)) {
                                $sizes[] = $sizeName;
                            }
                        }
                        
                        if (!empty($sizes)) {
                            $productAttributes['sizes'] = [
                                'en' => array_unique(array_filter($sizes)),
                                'ar' => array_unique(array_filter($sizes)),
                            ];
                        }
                    }
                    
                    // Create or update product
                    $product = Product::updateOrCreate(
                        ['sku' => 'SHEIN-' . $productSku],
                        $productAttributes
                    );
                    
                    $importedCount++;
                    $this->info("Imported product: {$product->name['en']}");
                    
                } catch (Exception $e) {
                    $productSku = isset($productData['goods_sn']) ? $productData['goods_sn'] : 'unknown';
                    Log::error('Failed to import Shein product: ' . $e->getMessage(), [
                        'product_id' => $productSku,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $this->error("Failed to import product {$productSku}: " . $e->getMessage());
                }
            }
            
            $this->info("Successfully imported {$importedCount} products from Shein");
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            Log::error('Shein product import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Shein product import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}