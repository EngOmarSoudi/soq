<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Address;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderSeeder extends Seeder
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

        $shippingAddress = Address::where('user_id', $john->id)
            ->where('label', 'Home')
            ->first();

        if (!$shippingAddress) {
            return;
        }

        $products = Product::take(8)->get();

        $orders = [
            [
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'bank_transfer',
                'items' => [
                    ['product' => $products[0] ?? null, 'quantity' => 1],
                    ['product' => $products[1] ?? null, 'quantity' => 2],
                ],
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'status' => 'processing',
                'payment_status' => 'completed',
                'payment_method' => 'credit_card',
                'items' => [
                    ['product' => $products[2] ?? null, 'quantity' => 1],
                    ['product' => $products[3] ?? null, 'quantity' => 1],
                ],
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'status' => 'shipped',
                'payment_status' => 'completed',
                'payment_method' => 'credit_card',
                'items' => [
                    ['product' => $products[4] ?? null, 'quantity' => 3],
                ],
                'created_at' => Carbon::now()->subDays(15),
                'shipped_at' => Carbon::now()->subDays(12),
            ],
            [
                'status' => 'delivered',
                'payment_status' => 'completed',
                'payment_method' => 'bank_transfer',
                'items' => [
                    ['product' => $products[5] ?? null, 'quantity' => 2],
                    ['product' => $products[6] ?? null, 'quantity' => 1],
                ],
                'created_at' => Carbon::now()->subDays(25),
                'shipped_at' => Carbon::now()->subDays(22),
                'delivered_at' => Carbon::now()->subDays(20),
            ],
            [
                'status' => 'delivered',
                'payment_status' => 'completed',
                'payment_method' => 'credit_card',
                'items' => [
                    ['product' => $products[7] ?? null, 'quantity' => 1],
                    ['product' => $products[0] ?? null, 'quantity' => 2],
                ],
                'created_at' => Carbon::now()->subDays(30),
                'shipped_at' => Carbon::now()->subDays(28),
                'delivered_at' => Carbon::now()->subDays(26),
            ],
            [
                'status' => 'cancelled',
                'payment_status' => 'refunded',
                'payment_method' => 'credit_card',
                'items' => [
                    ['product' => $products[3] ?? null, 'quantity' => 1],
                ],
                'created_at' => Carbon::now()->subDays(35),
            ],
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            $createdAt = $orderData['created_at'];
            unset($orderData['items'], $orderData['created_at']);

            // Calculate order totals
            $subtotal = 0;
            foreach ($items as $item) {
                if ($item['product']) {
                    $subtotal += $item['product']->price * $item['quantity'];
                }
            }

            $shippingCost = $orderData['status'] === 'cancelled' ? 0 : 5.00;
            $taxAmount = $subtotal * 0.1;
            $totalAmount = $subtotal + $shippingCost + $taxAmount;

            $order = Order::create(array_merge($orderData, [
                'order_number' => 'ORD-' . date('Ymd', $createdAt->timestamp) . '-' . Str::random(6),
                'user_id' => $john->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $shippingAddress->id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]));

            // Add order items
            foreach ($items as $item) {
                if ($item['product']) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->price,
                        'total_price' => $item['product']->price * $item['quantity'],
                    ]);
                }
            }
        }
    }
}
