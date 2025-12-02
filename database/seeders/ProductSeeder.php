<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Electronics
            [
                'category_slug' => 'electronics',
                'name' => ['en' => 'Wireless Bluetooth Headphones', 'ar' => 'سماعات بلوتوث لاسلكية'],
                'description' => ['en' => 'High-quality wireless headphones with noise cancellation', 'ar' => 'سماعات لاسلكية عالية الجودة مع إلغاء الضوضاء'],
                'sku' => 'WBH-001',
                'price' => 89.99,
                'cost_price' => 40.00,
                'stock_quantity' => 50,
                'is_featured' => true,
                'average_rating' => 4.5,
            ],
            [
                'category_slug' => 'electronics',
                'name' => ['en' => 'USB-C Fast Charger', 'ar' => 'شاحن سريع USB-C'],
                'description' => ['en' => 'Universal USB-C charger compatible with all devices', 'ar' => 'شاحن USB-C عالمي متوافق مع جميع الأجهزة'],
                'sku' => 'UFC-001',
                'price' => 29.99,
                'cost_price' => 12.00,
                'stock_quantity' => 100,
                'is_featured' => true,
                'average_rating' => 4.8,
            ],
            [
                'category_slug' => 'electronics',
                'name' => ['en' => 'Smart Watch Pro', 'ar' => 'ساعة ذكية برو'],
                'description' => ['en' => 'Feature-rich smartwatch with health tracking', 'ar' => 'ساعة ذكية غنية بالميزات مع تتبع الصحة'],
                'sku' => 'SWP-001',
                'price' => 199.99,
                'cost_price' => 80.00,
                'stock_quantity' => 30,
                'is_featured' => true,
                'average_rating' => 4.3,
            ],
            [
                'category_slug' => 'electronics',
                'name' => ['en' => 'Portable Power Bank', 'ar' => 'بنك الطاقة المحمول'],
                'description' => ['en' => '20000mAh power bank with fast charging', 'ar' => 'بنك طاقة 20000mAh مع الشحن السريع'],
                'sku' => 'PPB-001',
                'price' => 34.99,
                'cost_price' => 15.00,
                'stock_quantity' => 75,
                'is_featured' => false,
                'average_rating' => 4.6,
            ],
            // Clothing
            [
                'category_slug' => 'clothing',
                'name' => ['en' => 'Premium Cotton T-Shirt', 'ar' => 'تي شيرت قطن فاخر'],
                'description' => ['en' => 'Comfortable 100% cotton t-shirt for everyday wear', 'ar' => 'قميص قطن 100٪ مريح للارتداء اليومي'],
                'sku' => 'PCT-001',
                'price' => 19.99,
                'cost_price' => 8.00,
                'stock_quantity' => 200,
                'is_featured' => true,
                'average_rating' => 4.4,
            ],
            [
                'category_slug' => 'clothing',
                'name' => ['en' => 'Classic Denim Jeans', 'ar' => 'بنطال جينز كلاسيكي'],
                'description' => ['en' => 'Stylish and durable denim jeans', 'ar' => 'بنطال جينز أنيق وتقليدي'],
                'sku' => 'CDJ-001',
                'price' => 49.99,
                'cost_price' => 20.00,
                'stock_quantity' => 80,
                'is_featured' => true,
                'average_rating' => 4.7,
            ],
            [
                'category_slug' => 'clothing',
                'name' => ['en' => 'Winter Wool Jacket', 'ar' => 'سترة صوف الشتاء'],
                'description' => ['en' => 'Warm and comfortable wool jacket for cold weather', 'ar' => 'سترة صوف دافئة ومريحة للطقس البارد'],
                'sku' => 'WWJ-001',
                'price' => 79.99,
                'cost_price' => 35.00,
                'stock_quantity' => 40,
                'is_featured' => false,
                'average_rating' => 4.5,
            ],
            // Home & Garden
            [
                'category_slug' => 'home-garden',
                'name' => ['en' => 'LED Desk Lamp', 'ar' => 'مصباح مكتب LED'],
                'description' => ['en' => 'Energy-efficient LED desk lamp with adjustable brightness', 'ar' => 'مصباح مكتب LED موفر للطاقة مع سطوع قابل للتعديل'],
                'sku' => 'LDL-001',
                'price' => 39.99,
                'cost_price' => 18.00,
                'stock_quantity' => 60,
                'is_featured' => true,
                'average_rating' => 4.6,
            ],
            [
                'category_slug' => 'home-garden',
                'name' => ['en' => 'Bamboo Cutting Board Set', 'ar' => 'مجموعة لوح التقطيع من الخيزران'],
                'description' => ['en' => 'Eco-friendly bamboo cutting boards in various sizes', 'ar' => 'ألواح تقطيع من الخيزران صديقة للبيئة بأحجام مختلفة'],
                'sku' => 'BCS-001',
                'price' => 24.99,
                'cost_price' => 10.00,
                'stock_quantity' => 120,
                'is_featured' => false,
                'average_rating' => 4.5,
            ],
            [
                'category_slug' => 'home-garden',
                'name' => ['en' => 'Garden Tool Kit', 'ar' => 'مجموعة أدوات الحديقة'],
                'description' => ['en' => 'Complete set of essential garden tools', 'ar' => 'مجموعة كاملة من أدوات الحديقة الأساسية'],
                'sku' => 'GTK-001',
                'price' => 44.99,
                'cost_price' => 20.00,
                'stock_quantity' => 35,
                'is_featured' => true,
                'average_rating' => 4.7,
            ],
            // Sports & Outdoors
            [
                'category_slug' => 'sports-outdoors',
                'name' => ['en' => 'Professional Yoga Mat', 'ar' => 'حصيرة يوجا احترافية'],
                'description' => ['en' => 'Non-slip yoga mat for exercise and meditation', 'ar' => 'حصيرة يوجا مضادة للانزلاق للتمرين والتأمل'],
                'sku' => 'PYM-001',
                'price' => 34.99,
                'cost_price' => 15.00,
                'stock_quantity' => 70,
                'is_featured' => true,
                'average_rating' => 4.8,
            ],
            [
                'category_slug' => 'sports-outdoors',
                'name' => ['en' => 'Running Sports Watch', 'ar' => 'ساعة الرياضة للجري'],
                'description' => ['en' => 'GPS-enabled sports watch for runners', 'ar' => 'ساعة رياضة مع تقنية GPS للعدائين'],
                'sku' => 'RSW-001',
                'price' => 149.99,
                'cost_price' => 60.00,
                'stock_quantity' => 25,
                'is_featured' => false,
                'average_rating' => 4.6,
            ],
            // Books & Media
            [
                'category_slug' => 'books-media',
                'name' => ['en' => 'The Art of Code', 'ar' => 'فن البرمجة'],
                'description' => ['en' => 'Comprehensive guide to clean code and best practices', 'ar' => 'دليل شامل للكود النظيف وأفضل الممارسات'],
                'sku' => 'TAC-001',
                'price' => 39.99,
                'cost_price' => 15.00,
                'stock_quantity' => 90,
                'is_featured' => true,
                'average_rating' => 4.9,
            ],
            // Beauty & Personal Care
            [
                'category_slug' => 'beauty-care',
                'name' => ['en' => 'Organic Face Moisturizer', 'ar' => 'مرطب الوجه العضوي'],
                'description' => ['en' => '100% organic facial moisturizer for all skin types', 'ar' => 'مرطب وجه عضوي 100٪ لجميع أنواع البشرة'],
                'sku' => 'OFM-001',
                'price' => 24.99,
                'cost_price' => 10.00,
                'stock_quantity' => 150,
                'is_featured' => true,
                'average_rating' => 4.7,
            ],
            [
                'category_slug' => 'beauty-care',
                'name' => ['en' => 'Premium Shampoo & Conditioner Combo', 'ar' => 'مجموعة شامبو وبلسم فاخرة'],
                'description' => ['en' => 'Natural ingredients shampoo and conditioner set', 'ar' => 'مجموعة شامبو وبلسم بمكونات طبيعية'],
                'sku' => 'PSC-001',
                'price' => 19.99,
                'cost_price' => 8.00,
                'stock_quantity' => 180,
                'is_featured' => false,
                'average_rating' => 4.6,
            ],
            // Food & Beverages
            [
                'category_slug' => 'food-beverages',
                'name' => ['en' => 'Premium Coffee Beans', 'ar' => 'حبوب القهوة الفاخرة'],
                'description' => ['en' => 'Single-origin arabica coffee beans - freshly roasted', 'ar' => 'حبوب قهوة أرابيكا من منشأ واحد محمصة طازجة'],
                'sku' => 'PCB-001',
                'price' => 14.99,
                'cost_price' => 5.00,
                'stock_quantity' => 200,
                'is_featured' => true,
                'average_rating' => 4.8,
            ],
            [
                'category_slug' => 'food-beverages',
                'name' => ['en' => 'Organic Green Tea Set', 'ar' => 'مجموعة الشاي الأخضر العضوي'],
                'description' => ['en' => 'Premium organic green tea collection', 'ar' => 'مجموعة الشاي الأخضر العضوي الفاخر'],
                'sku' => 'OGT-001',
                'price' => 19.99,
                'cost_price' => 7.00,
                'stock_quantity' => 100,
                'is_featured' => false,
                'average_rating' => 4.5,
            ],
            // Toys & Games
            [
                'category_slug' => 'toys-games',
                'name' => ['en' => 'Educational STEM Puzzle', 'ar' => 'لغز STEM التعليمي'],
                'description' => ['en' => 'Interactive puzzle for learning science and math', 'ar' => 'لغز تفاعلي للتعلم العلمي والرياضي'],
                'sku' => 'ESP-001',
                'price' => 29.99,
                'cost_price' => 12.00,
                'stock_quantity' => 85,
                'is_featured' => true,
                'average_rating' => 4.7,
            ],
            [
                'category_slug' => 'toys-games',
                'name' => ['en' => 'Board Game Collection', 'ar' => 'مجموعة ألعاب الطاولة'],
                'description' => ['en' => 'Family-friendly board games collection', 'ar' => 'مجموعة ألعاب طاولة صديقة للعائلة'],
                'sku' => 'BGC-001',
                'price' => 44.99,
                'cost_price' => 18.00,
                'stock_quantity' => 55,
                'is_featured' => false,
                'average_rating' => 4.6,
            ],
        ];

        foreach ($products as $productData) {
            $categorySlug = $productData['category_slug'];
            unset($productData['category_slug']);
            
            $category = Category::where('slug', $categorySlug)->first();
            
            if ($category) {
                $productData['category_id'] = $category->id;
                $productData['slug'] = Str::slug($productData['name']['en']);
                $productData['is_active'] = true;
                
                Product::create($productData);
            }
        }
    }
}
