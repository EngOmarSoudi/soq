<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class ListCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all coupons in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coupons = Coupon::all();
        
        if ($coupons->isEmpty()) {
            $this->info('No coupons found in the database.');
            return;
        }
        
        $this->table(
            ['ID', 'Code', 'Type', 'Value', 'Min Order', 'Max Discount', 'Active', 'Valid From', 'Valid Until'],
            $coupons->map(function ($coupon) {
                return [
                    $coupon->id,
                    $coupon->code,
                    $coupon->discount_type,
                    $coupon->discount_value,
                    $coupon->minimum_order_amount ?? 'N/A',
                    $coupon->maximum_discount ?? 'N/A',
                    $coupon->is_active ? 'Yes' : 'No',
                    $coupon->valid_from ? $coupon->valid_from->format('Y-m-d H:i:s') : 'N/A',
                    $coupon->valid_until ? $coupon->valid_until->format('Y-m-d H:i:s') : 'N/A',
                ];
            })->toArray()
        );
    }
}