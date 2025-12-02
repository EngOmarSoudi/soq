<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CheckOrderDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:check {orderId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check order details for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('orderId');
        $order = Order::with(['coupon', 'items.product'])->find($orderId);
        
        if (!$order) {
            $this->error("Order #{$orderId} not found");
            return;
        }
        
        $this->info("Order #{$order->order_number} Details:");
        $this->line("Subtotal: SAR " . number_format($order->subtotal, 2));
        $this->line("Shipping: SAR " . number_format($order->shipping_cost, 2));
        $this->line("Tax: SAR " . number_format($order->tax_amount, 2));
        $this->line("Discount: SAR " . number_format($order->discount_amount, 2));
        $this->line("Total: SAR " . number_format($order->total_amount, 2));
        
        if ($order->coupon) {
            $this->info("Coupon Applied:");
            $this->line("Code: {$order->coupon->code}");
            $this->line("ID: {$order->coupon->id}");
            $this->line("Discount Value: " . ($order->coupon->discount_type === 'percentage' ? $order->coupon->discount_value . '%' : 'SAR ' . number_format($order->coupon->discount_value, 2)));
        } else {
            $this->info("No coupon applied");
        }
    }
}