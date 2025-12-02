<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Electronics', 'ar' => 'الإلكترونيات'],
                'description' => ['en' => 'Electronic devices and gadgets', 'ar' => 'الأجهزة الإلكترونية والملحقات'],
                'slug' => 'electronics',
            ],
            [
                'name' => ['en' => 'Clothing', 'ar' => 'الملابس'],
                'description' => ['en' => 'Fashion and apparel', 'ar' => 'الملابس والأزياء'],
                'slug' => 'clothing',
            ],
            [
                'name' => ['en' => 'Home & Garden', 'ar' => 'المنزل والحديقة'],
                'description' => ['en' => 'Home furniture and garden tools', 'ar' => 'الأثاث المنزلي وأدوات الحديقة'],
                'slug' => 'home-garden',
            ],
            [
                'name' => ['en' => 'Sports & Outdoors', 'ar' => 'الرياضة والأنشطة الخارجية'],
                'description' => ['en' => 'Sports equipment and outdoor gear', 'ar' => 'معدات الرياضة والتجهيزات الخارجية'],
                'slug' => 'sports-outdoors',
            ],
            [
                'name' => ['en' => 'Books & Media', 'ar' => 'الكتب والوسائط'],
                'description' => ['en' => 'Books, movies, and music', 'ar' => 'الكتب والأفلام والموسيقى'],
                'slug' => 'books-media',
            ],
            [
                'name' => ['en' => 'Beauty & Personal Care', 'ar' => 'الجمال والعناية الشخصية'],
                'description' => ['en' => 'Beauty products and personal care items', 'ar' => 'منتجات الجمال وأدوات العناية الشخصية'],
                'slug' => 'beauty-care',
            ],
            [
                'name' => ['en' => 'Food & Beverages', 'ar' => 'الغذاء والمشروبات'],
                'description' => ['en' => 'Gourmet food and beverages', 'ar' => 'الأغذية الفاخرة والمشروبات'],
                'slug' => 'food-beverages',
            ],
            [
                'name' => ['en' => 'Toys & Games', 'ar' => 'الألعاب'],
                'description' => ['en' => 'Toys and games for all ages', 'ar' => 'الألعاب لجميع الأعمار'],
                'slug' => 'toys-games',
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create(array_merge($categoryData, [
                'is_active' => true,
                'sort_order' => 0,
            ]));
        }
    }
}
