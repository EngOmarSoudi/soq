<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JohnUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create john@example.com user
        $john = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1-555-0101',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        // Add multiple shipping addresses for john
        $addresses = [
            [
                'label' => 'Home',
                'street_address' => '456 Main Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90001',
                'country' => 'United States',
                'phone' => '+1-555-0101',
                'is_default' => true,
            ],
            [
                'label' => 'Office',
                'street_address' => '789 Business Park, Suite 100',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90002',
                'country' => 'United States',
                'phone' => '+1-555-0102',
                'is_default' => false,
            ],
            [
                'label' => 'Parent\'s House',
                'street_address' => '321 Oak Street',
                'city' => 'San Francisco',
                'state' => 'CA',
                'postal_code' => '94102',
                'country' => 'United States',
                'phone' => '+1-555-0103',
                'is_default' => false,
            ],
        ];

        foreach ($addresses as $addressData) {
            Address::create(array_merge($addressData, [
                'user_id' => $john->id,
                'name' => 'John Doe',
            ]));
        }
    }
}
