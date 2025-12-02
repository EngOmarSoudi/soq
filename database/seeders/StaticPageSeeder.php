<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StaticPage::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Us',
                'content' => '<h1>About Our Company</h1><p>Welcome to our e-commerce platform. We are dedicated to providing the best products and services to our customers.</p>',
                'meta_description' => 'Learn more about our company and our mission.',
                'is_active' => true,
            ]
        );

        StaticPage::updateOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Us',
                'content' => '<h1>Contact Information</h1><p>If you have any questions or need assistance, please feel free to contact us.</p><ul><li>Email: info@example.com</li><li>Phone: +1 (555) 123-4567</li><li>Address: 123 Main Street, City, Country</li></ul>',
                'meta_description' => 'Get in touch with us for any inquiries or support.',
                'is_active' => true,
            ]
        );

        StaticPage::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<h1>Privacy Policy</h1><p>We are committed to protecting your privacy and personal information. This policy explains how we collect, use, and protect your data.</p>',
                'meta_description' => 'Our privacy policy explains how we handle your personal information.',
                'is_active' => true,
            ]
        );

        StaticPage::updateOrCreate(
            ['slug' => 'terms-of-service'],
            [
                'title' => 'Terms of Service',
                'content' => '<h1>Terms of Service</h1><p>By using our website and services, you agree to the following terms and conditions.</p>',
                'meta_description' => 'Terms and conditions for using our website and services.',
                'is_active' => true,
            ]
        );
    }
}