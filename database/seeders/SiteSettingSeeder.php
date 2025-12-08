<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSetting::updateOrCreate(
            [],
            [
                'site_name' => 'My E-Commerce Store',
                'site_email' => 'info@myecommercestore.com',
                'site_phone' => '+1 (555) 123-4567',
                'site_address' => '123 Main Street, City, Country',
                'site_address_map_url' => 'https://maps.app.goo.gl/JNqM3VBu9CXD6bE39',
                'site_description' => 'Welcome to our online store. We offer the best products at competitive prices.',
                'facebook_url' => 'https://facebook.com/myecommercestore',
                'twitter_url' => 'https://twitter.com/myecommercestore',
                'instagram_url' => 'https://instagram.com/myecommercestore',
                'linkedin_url' => 'https://linkedin.com/company/myecommercestore',
            ]
        );
    }
}
