<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CouponSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update a coupon that's valid for one day from today
        Coupon::updateOrCreate([
            'code' => 'ONEDAY2025',
        ], [
            'name' => json_encode([
                'en' => 'One Day Special',
                'ar' => 'عرض خاص ليوم واحد'
            ]),
            'description' => json_encode([
                'en' => 'Special discount valid for one day only',
                'ar' => 'خصم خاص صالح ليوم واحد فقط'
            ]),
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'usage_limit' => 100,
            'usage_count' => 0,
            'per_user_limit' => 1,
            'minimum_order_amount' => 50.00,
            'maximum_discount' => 50.00,
            'valid_from' => Carbon::today(),
            'valid_until' => Carbon::tomorrow()->endOfDay(),
            'is_active' => true,
        ]);

        // Create or update another coupon that's valid for one day from today with fixed amount discount
        Coupon::updateOrCreate([
            'code' => 'FIXED10',
        ], [
            'name' => json_encode([
                'en' => 'Fixed $10 Off',
                'ar' => 'خصم ثابت 10 دولارات'
            ]),
            'description' => json_encode([
                'en' => '$10 off your order for one day only',
                'ar' => 'خصم 10 دولارات على طلبك ليوم واحد فقط'
            ]),
            'discount_type' => 'fixed',
            'discount_value' => 10.00,
            'usage_limit' => 50,
            'usage_count' => 0,
            'per_user_limit' => 1,
            'minimum_order_amount' => 25.00,
            'maximum_discount' => null,
            'valid_from' => Carbon::today(),
            'valid_until' => Carbon::tomorrow()->endOfDay(),
            'is_active' => true,
        ]);
    }
}