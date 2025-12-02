<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '+1-555-0100',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Add admin default address
        Address::create([
            'user_id' => $admin->id,
            'label' => 'Admin Office',
            'name' => 'Admin User',
            'street_address' => '123 Admin Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'United States',
            'phone' => '+1-555-0100',
            'is_default' => true,
        ]);

        // Create Regular Customer Users
        $customers = [
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '+1-555-0102',
                'address_label' => 'Home',
                'street_address' => '789 Oak Drive',
                'city' => 'Chicago',
                'state' => 'IL',
                'postal_code' => '60601',
                'country' => 'United States',
            ],
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed@example.com',
                'phone' => '+966-50-1234567',
                'address_label' => 'Home',
                'street_address' => '321 Prince Road',
                'city' => 'Riyadh',
                'state' => '',
                'postal_code' => '11411',
                'country' => 'Saudi Arabia',
            ],
            [
                'name' => 'Sara Johnson',
                'email' => 'sara@example.com',
                'phone' => '+1-555-0103',
                'address_label' => 'Office',
                'street_address' => '555 Business Plaza',
                'city' => 'Houston',
                'state' => 'TX',
                'postal_code' => '77001',
                'country' => 'United States',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael@example.com',
                'phone' => '+1-555-0104',
                'address_label' => 'Home',
                'street_address' => '999 Maple Street',
                'city' => 'Phoenix',
                'state' => 'AZ',
                'postal_code' => '85001',
                'country' => 'United States',
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'is_active' => true,
            ]);

            // Create default address for customer
            Address::create([
                'user_id' => $customer->id,
                'label' => $customerData['address_label'],
                'name' => $customerData['name'],
                'street_address' => $customerData['street_address'],
                'city' => $customerData['city'],
                'state' => $customerData['state'],
                'postal_code' => $customerData['postal_code'],
                'country' => $customerData['country'],
                'phone' => $customerData['phone'],
                'is_default' => true,
            ]);
        }

        // Call individual seeders
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            AliExpressProductSeeder::class,
            JohnUserSeeder::class,
            CartItemSeeder::class,
            OrderSeeder::class,
            CouponSeeder::class,
        ]);
    }
}