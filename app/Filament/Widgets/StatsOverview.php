<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Customer Metrics
        $totalCustomers = \App\Models\User::count();
        // Active Users: Logged in or ordered in last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $activeCustomers = \App\Models\User::where(function($query) use ($thirtyDaysAgo) {
            $query->where('last_login_at', '>=', $thirtyDaysAgo)
                  ->orWhereHas('orders', function($q) use ($thirtyDaysAgo) {
                      $q->where('created_at', '>=', $thirtyDaysAgo);
                  });
        })->count();
        
        // Product Views
        $totalViews = \App\Models\Product::sum('views_count') ?? 0;
        
        // Sales Data (Revenue)
        // Including all orders that are paid or delivered/shipped/completed
        $revenueQuery = \App\Models\Order::query()
            ->where('payment_status', 'completed')
            ->orWhereIn('status', ['completed', 'delivered', 'shipped']);
            
        $totalRevenue = $revenueQuery->sum('total_amount');
        $totalItemsSold = \App\Models\OrderItem::whereIn('order_id', $revenueQuery->pluck('id'))->sum('quantity');

        // Order Stats
        $totalOrders = \App\Models\Order::count();
        $completedOrders = \App\Models\Order::where('status', 'completed')->count();
        $shippedOrders = \App\Models\Order::where('status', 'shipped')->count();
        $deliveredOrders = \App\Models\Order::where('status', 'delivered')->count();
        $canceledOrders = \App\Models\Order::where('status', 'cancelled')->orWhere('status', 'canceled')->count();
        
        // Inventory Investment
        // Sum of (stock * real_cost). If real_cost is null, it treats it as 0.
        // We'll trust the real_cost field as per user request.
        $inventoryInvestment = \App\Models\Product::whereNotNull('real_cost')
            ->sum(\Illuminate\Support\Facades\DB::raw('stock_quantity * real_cost'));
        
        // Product Count with Missing Cost (for context, maybe not in stat but good to know)
        // $productsWithMissingCost = \App\Models\Product::whereNull('real_cost')->count();
        
        // Profit Analysis
        // Revenue - Cost of Goods Sold (for Sold items only)
        // We only calculate COGS for the orders that contributed to Revenue
        $revenueOrderIds = $revenueQuery->pluck('id');
        
        $costOfGoodsSold = \App\Models\OrderItem::whereIn('order_id', $revenueOrderIds)
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(\Illuminate\Support\Facades\DB::raw('order_items.quantity * COALESCE(products.real_cost, 0)'));
            
        $estimatedProfit = $totalRevenue - $costOfGoodsSold;

        return [
            Stat::make('Total Customers', $totalCustomers)
                ->description("Active (30d): $activeCustomers")
                ->descriptionIcon('heroicon-m-users')
                ->chart([$activeCustomers, $totalCustomers])
                ->color('success')
                ->url(\App\Filament\Resources\Users\UserResource::getUrl('index', ['tableFilters' => ['recently_active' => ['isActive' => true]]])),
                
            Stat::make('Product Views', number_format($totalViews))
                ->description("Across all products")
                ->descriptionIcon('heroicon-m-eye')
                ->color('info')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl()),
                
            Stat::make('Total Revenue', 'SAR ' . number_format($totalRevenue - $revenueQuery->sum('shipping_cost'), 2))
                ->description("Excluding Shipping (Gross: " . number_format($totalRevenue, 2) . ")")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([$totalRevenue])
                ->color('success')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),

            Stat::make('Shipping Revenue', 'SAR ' . number_format($revenueQuery->sum('shipping_cost'), 2))
                ->description("Total collection from shipping fees")
                ->descriptionIcon('heroicon-m-truck')
                ->color('gray')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
                
            Stat::make('Orders', $totalOrders)
                ->description("Delivered: $deliveredOrders, Shipped: $shippedOrders, Canceled: $canceledOrders")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning')
                ->url(\App\Filament\Resources\Orders\OrderResource::getUrl()),
                
            Stat::make('Inventory Investment', 'SAR ' . number_format($inventoryInvestment, 2))
                ->description('Value based on Real Cost')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger')
                ->url(\App\Filament\Resources\Products\ProductResource::getUrl()),
                
            Stat::make('Estimated Profit', 'SAR ' . number_format(($totalRevenue - $revenueQuery->sum('shipping_cost')) - $costOfGoodsSold, 2))
                ->description('Net Revenue - Cost of Goods')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('success'),
        ];
    }
}
