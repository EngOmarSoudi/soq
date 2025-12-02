<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('platform')->nullable()->after('delivered_at');
            $table->string('platform_order_id')->nullable()->after('platform');
            $table->string('tracking_number')->nullable()->after('platform_order_id');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->string('shipping_status')->nullable()->after('tracking_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['platform', 'platform_order_id', 'tracking_number', 'tracking_url', 'shipping_status']);
        });
    }
};