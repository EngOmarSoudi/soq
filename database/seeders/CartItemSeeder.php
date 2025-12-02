<?php

namespace Database\Seeders;

use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $john = User::where('email', 'john@example.com')->first();
        
        if (!$john) {
            return;
        }

        // Get some products for the cart
        $products = Product::take(5)->get();

        $cartItems = [
            [
                'product' => $products[0] ?? null,
                'quantity' => 2,
            ],
            [
                'product' => $products[1] ?? null,
                'quantity' => 1,
            ],
            [
                'product' => $products[2] ?? null,
                'quantity' => 3,
            ],
        ];

        foreach ($cartItems as $item) {
            if ($item['product']) {
                CartItem::create([
                    'user_id' => $john->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->price,
                ]);
            }
        }
    }
}
