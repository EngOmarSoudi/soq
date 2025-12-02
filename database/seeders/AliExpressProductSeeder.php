<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Services\AliExpressService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AliExpressProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Create an instance of AliExpressService
            $aliExpressService = new AliExpressService();
            
            // The provided AliExpress URL
            $url = 'https://ar.aliexpress.com/item/1005009895935503.html?spm=a2g0o.detail.pcDetailBottomMoreOtherSeller.1.6de5YeVBYeVBQG&gps-id=pcDetailBottomMoreOtherSeller&scm=1007.40050.354490.0&scm_id=1007.40050.354490.0&scm-url=1007.40050.354490.0&pvid=ab4763ed-8359-4970-90ff-e1e258ff5953&_t=gps-id:pcDetailBottomMoreOtherSeller,scm-url:1007.40050.354490.0,pvid:ab4763ed-8359-4970-90ff-e1e258ff5953,tpp_buckets:668%232846%238112%231997&pdp_ext_f=%7B%22order%22%3A%22151%22%2C%22eval%22%3A%221%22%2C%22sceneId%22%3A%2230050%22%2C%22fromPage%22%3A%22recommend%22%7D&pdp_npi=6%40dis%21SAR%21101.94%2134.55%21%21%21187.46%2163.54%21%40212e520d17641553674866181ec548%2112000050511195169%21rec%21SA%216312531209%21ABXZ%211%210%21n_tag%3A-29910%3Bd%3A6f1493ce%3Bm03_new_user%3A-29895%3BpisId%3A5000000187470327&utparam-url=scene%3ApcDetailBottomMoreOtherSeller%7Cquery_from%3A%7Cx_object_id%3A1005009895935503%7C_p_origin_prod%3A';
            
            // Fetch product details from AliExpress
            $productData = $aliExpressService->fetchProductDetails($url);
            
            // Find an appropriate category (using electronics as default)
            $category = Category::where('slug', 'electronics')->first();
            
            if (!$category) {
                // If electronics category doesn't exist, use the first available category
                $category = Category::first();
            }
            
            if (!$category) {
                Log::error('No categories found in the database');
                return;
            }
            
            // Generate a slug for the product
            $slug = Str::slug($productData['name']['en'] ?? 'aliexpress-product') . '-' . time();
            
            // Prepare product data for creation
            $productAttributes = [
                'name' => $productData['name'],
                'description' => $productData['description'],
                'sku' => 'AE-' . $productData['product_id'],
                'slug' => $slug,
                'price' => $productData['price'],
                'category_id' => $category->id,
                'supplier_type' => 'online',
                'supplier_link' => $url,
                'is_active' => true,
                'is_featured' => true,
            ];
            
            // Handle product images
            if (!empty($productData['images'])) {
                // Download and store images locally
                $imagePaths = $aliExpressService->downloadProductImages($productData['images'], $productData['product_id']);
                if (!empty($imagePaths)) {
                    // Set main image
                    $productAttributes['image'] = $imagePaths[0];
                    
                    // Set additional images
                    $productAttributes['images'] = array_slice($imagePaths, 1);
                }
            }
            
            // Set colors and sizes if available
            if (!empty($productData['attributes']['color'])) {
                $productAttributes['colors'] = [
                    'en' => $productData['attributes']['color'],
                    'ar' => $productData['attributes']['color'] // In a real implementation, you would translate these
                ];
            }
            
            if (!empty($productData['attributes']['size'])) {
                $productAttributes['sizes'] = [
                    'en' => $productData['attributes']['size'],
                    'ar' => $productData['attributes']['size'] // In a real implementation, you would translate these
                ];
            }
            
            // Create the product
            $product = Product::create($productAttributes);
            
            Log::info('AliExpress product seeded successfully', ['product_id' => $product->id, 'sku' => $product->sku]);
            
            echo "AliExpress product seeded successfully with ID: {$product->id} and SKU: {$product->sku}\n";
            
        } catch (\Exception $e) {
            Log::error('Failed to seed AliExpress product: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            echo "Failed to seed AliExpress product: " . $e->getMessage() . "\n";
        }
    }
}