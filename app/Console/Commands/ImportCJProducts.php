<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\API\CJService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportCJProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cj-products {keyword?} {--page=1 : Page number to import} {--limit=20 : Number of products per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from CJ API';

    /**
     * Execute the console command.
     */
    public function handle(CJService $cjService)
    {
        try {
            $this->info('Starting CJ product import...');
            
            $keyword = $this->argument('keyword') ? $this->argument('keyword') : 'electronics';
            $page = $this->option('page');
            $limit = $this->option('limit');
            
            // Search products from CJ
            $response = $cjService->searchProducts($keyword, $page, $limit);
            
            if (!isset($response['data']['productList'])) {
                $this->error('No products found in CJ API response');
                return Command::FAILURE;
            }
            
            $products = $response['data']['productList'];
            $importedCount = 0;
            
            $this->info("Found " . count($products) . " products. Importing page {$page}...");
            
            foreach ($products as $productData) {
                try {
                    // Get detailed product information
                    $detailResponse = $cjService->getProductDetails($productData['pid']);
                    
                    if (!isset($detailResponse['data'])) {
                        $this->warn("Failed to get details for product {$productData['pid']}");
                        continue;
                    }
                    
                    $detail = $detailResponse['data'];
                    
                    // Find or create category
                    $categoryName = isset($detail['categoryName']) ? $detail['categoryName'] : 'Uncategorized';
                    $category = Category::firstOrCreate(
                        ['slug' => str_slug($categoryName)],
                        [
                            'name' => ['en' => $categoryName, 'ar' => $categoryName],
                            'description' => ['en' => '', 'ar' => ''],
                            'is_active' => true,
                        ]
                    );
                    
                    // Prepare product data
                    $productName = isset($detail['name']) ? $detail['name'] : 'Unknown Product';
                    $productDescription = isset($detail['description']) ? $detail['description'] : '';
                    $productPrice = isset($detail['price']) ? $detail['price'] : 0;
                    $productUrl = isset($detail['url']) ? $detail['url'] : '';
                    $productStock = isset($detail['availableStock']) ? $detail['availableStock'] : 0;
                    
                    $productAttributes = [
                        'name' => [
                            'en' => $productName,
                            'ar' => $productName,
                        ],
                        'description' => [
                            'en' => $productDescription,
                            'ar' => $productDescription,
                        ],
                        'sku' => 'CJ-' . $detail['pid'],
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
                    if (!empty($detail['productSku'])) {
                        $colors = [];
                        $sizes = [];
                        
                        foreach ($detail['productSku'] as $variant) {
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
                        ['sku' => 'CJ-' . $detail['pid']],
                        $productAttributes
                    );
                    
                    $importedCount++;
                    $this->info("Imported product: {$product->name['en']}");
                    
                } catch (Exception $e) {
                    $productId = isset($productData['pid']) ? $productData['pid'] : 'unknown';
                    Log::error('Failed to import CJ product: ' . $e->getMessage(), [
                        'product_id' => $productId,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $this->error("Failed to import product {$productId}: " . $e->getMessage());
                }
            }
            
            $this->info("Successfully imported {$importedCount} products from CJ");
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            Log::error('CJ product import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('CJ product import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}