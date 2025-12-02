<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('order_number')
                    ->disabled(),
                
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                
                Select::make('shipping_address_id')
                    ->relationship('shippingAddress', 'label')
                    ->required(),
                
                Select::make('billing_address_id')
                    ->relationship('billingAddress', 'label'),
                
                TextInput::make('subtotal')
                    ->numeric()
                    ->disabled()
                    ->prefix('SAR'),
                
                TextInput::make('shipping_cost')
                    ->numeric()
                    ->prefix('SAR'),
                
                TextInput::make('tax_amount')
                    ->numeric()
                    ->prefix('SAR'),
                
                // Coupon information
                Placeholder::make('coupon_info')
                    ->label('Coupon Details')
                    ->content(fn ($record) => $record && ($record->coupon || $record->coupon_id) ? 
                        self::getCouponInfoContent($record) : 
                        'No coupon applied')
                    ->columnSpanFull()
                    ->html()
                    ->visible(fn ($record) => $record && ($record->coupon_id || $record->discount_amount > 0)),
                
                TextInput::make('discount_amount')
                    ->numeric()
                    ->prefix('SAR'),
                
                TextInput::make('total_amount')
                    ->numeric()
                    ->disabled()
                    ->prefix('SAR'),
                
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                    ])
                    ->required(),
                
                Select::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                    ])
                    ->required(),
                
                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->required(),
                
                TextInput::make('payment_reference'),
                
                // Display receipt image if it exists
                TextInput::make('receipt_url')
                    ->label('Receipt')
                    ->formatStateUsing(fn ($record) => $record?->receipt_url)
                    ->visible(fn ($record) => $record && $record->payment_method === 'bank_transfer' && $record->receipt_url)
                    ->suffixAction(
                        fn ($record) => $record && $record->receipt_url 
                            ? \Filament\Actions\Action::make('view_receipt')
                                ->label('View Receipt')
                                ->icon('heroicon-o-eye')
                                ->url($record->receipt_url, true)
                            : null
                    ),
                
                Textarea::make('notes'),
                
                // Order Items
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Placeholder::make('product_info')
                            ->label('Product')
                            ->content(fn ($record) => $record && $record->product ? 
                                self::getProductInfoContent($record) : 
                                'No product information')
                            ->columnSpan(2)
                            ->html(),
                        
                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        
                        TextInput::make('unit_price')
                            ->numeric()
                            ->required()
                            ->prefix('SAR'),
                        
                        TextInput::make('total_price')
                            ->numeric()
                            ->disabled()
                            ->prefix('SAR'),
                        
                        TextInput::make('color')
                            ->placeholder('No color selected')
                            ->disabled(),
                        
                        TextInput::make('size')
                            ->placeholder('No size selected')
                            ->disabled(),
                        
                        Placeholder::make('supplier_link')
                            ->label('Supplier Link')
                            ->content(fn ($record) => $record && $record->product ? 
                                self::getSupplierLinkContent($record) : 
                                'No supplier information')
                            ->columnSpanFull()
                            ->html(),
                    ])
                    ->columns(3)
                    ->columnSpan('full')
                    ->disabled()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('user.name')
                    ->sortable(),
                
                TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state) => 'SAR ' . number_format($state, 2))
                    ->sortable(),
                
                TextColumn::make('coupon.code')
                    ->label('Coupon')
                    ->badge()
                    ->color('success')
                    ->default('None')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('discount_amount')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'SAR ' . number_format($state, 2) : 'None')
                    ->sortable()
                    ->color('success'),
                
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),
                
                BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                    ]),
                
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                // Temporarily removed actions due to missing class error
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getProductInfoContent($record): string
    {
        if (!$record || !$record->product) {
            return 'No product information';
        }

        $product = $record->product;
        $productName = is_array($product->name) ? 
            ($product->name[app()->getLocale()] ?? $product->name['en'] ?? reset($product->name)) : 
            $product->name;

        // Get image URL
        $imageUrl = $product->image ? asset('storage/' . $product->image) : null;

        // Build the content
        $content = '<div class="flex items-center gap-3">';
        
        if ($imageUrl) {
            $content .= '<img src="' . e($imageUrl) . '" alt="' . e($productName) . '" class="w-12 h-12 object-cover rounded">';
        } else {
            $content .= '<div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">';
            $content .= '<svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
            $content .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>';
            $content .= '</svg>';
            $content .= '</div>';
        }
        
        $content .= '<div>';
        $content .= '<div class="font-medium">';
        $content .= '<a href="' . e('/products/' . $product->slug) . '" class="text-primary hover:underline">' . e($productName) . ' slug: '. e($product->slug) . '</a>';
        $content .= '</div>';
        $content .= '<div class="text-xs text-gray-500">SKU: ' . e($product->sku ?? 'N/A') . '</div>';
        $content .= '</div>';
        $content .= '</div>';
        
        return $content;
    }

    public static function getSupplierLinkContent($record): string
    {
        if (!$record || !$record->product) {
            return 'No supplier information';
        }

        $product = $record->product;
        
        $content = '<div class="space-y-2">';
        $content .= '<div class="text-sm">';
        $content .= '<span class="font-medium">Supplier Type:</span> ';  
        $content .= '<span class="capitalize">' . e($product->supplier_type ?? 'N/A') . '</span>';
        $content .= '</div>';
        
        if ($product->supplier_type === 'online' && $product->supplier_link) {
            $content .= '<div class="text-sm">';
            $content .= '<span class="font-medium">Product Link:</span> ';
            $content .= '<a href="' . e($product->supplier_link) . '" target="_blank" class="text-primary hover:underline inline-flex items-center gap-1">';
            $content .= 'View Product';
            $content .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
            $content .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>';
            $content .= '</svg>';
            $content .= '</a>';
            $content .= '</div>';
        }
        
        $content .= '</div>';
        
        return $content;
    }
    
    public static function getCouponInfoContent($record): string
    {
        if (!$record) {
            return 'No order information';
        }

        $content = '<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">';
        $content .= '<div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">';
        $content .= '<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Discount Information</h3>';
        $content .= '</div>';
        
        $content .= '<div class="p-4">';
        
        if ($record->coupon || $record->coupon_id) {
            $coupon = $record->coupon ?: \App\Models\Coupon::find($record->coupon_id);
            
            if ($coupon) {
                $couponName = is_array($coupon->name) ? 
                    ($coupon->name[app()->getLocale()] ?? $coupon->name['en'] ?? reset($coupon->name)) : 
                    $coupon->name;
                
                // Coupon details card
                $content .= '<div class="mb-4">';
                $content .= '<h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Coupon Details</h4>';
                $content .= '<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">';
                
                $content .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">';
                
                // Coupon code
                $content .= '<div class="flex items-center justify-between py-1">';
                $content .= '<span class="text-sm font-medium text-gray-600 dark:text-gray-300">Coupon Code:</span>';
                $content .= '<span class="font-mono text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">' . e($coupon->code) . '</span>';
                $content .= '</div>';
                
                // Coupon name
                $content .= '<div class="flex items-center justify-between py-1">';
                $content .= '<span class="text-sm font-medium text-gray-600 dark:text-gray-300">Name:</span>';
                $content .= '<span class="text-sm">' . e($couponName) . '</span>';
                $content .= '</div>';
                
                // Discount value
                $content .= '<div class="flex items-center justify-between py-1">';
                $content .= '<span class="text-sm font-medium text-gray-600 dark:text-gray-300">Discount:</span>';
                if ($coupon->discount_type === 'percentage') {
                    $content .= '<span class="text-sm font-medium text-green-600">' . e($coupon->discount_value) . '%</span>';
                } else {
                    $content .= '<span class="text-sm font-medium text-green-600">SAR ' . number_format($coupon->discount_value, 2) . '</span>';
                }
                $content .= '</div>';
                
                $content .= '</div>'; // end grid
                $content .= '</div>'; // end bg div
                $content .= '</div>'; // end coupon details card
            } else {
                $content .= '<div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">';
                $content .= '<p class="text-red-800 dark:text-red-200 text-sm">Coupon ID #' . e($record->coupon_id) . ' not found in database</p>';
                $content .= '</div>';
            }
        } else {
            $content .= '<div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">';
            $content .= '<p class="text-gray-600 dark:text-gray-400 text-sm">No coupon was applied to this order.</p>';
            $content .= '</div>';
        }
        
        // Order totals card
        $content .= '<div>';
        $content .= '<h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Order Totals</h4>';
        $content .= '<div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">';
        
        $subtotal = $record->subtotal;
        $shipping = $record->shipping_cost;
        $tax = $record->tax_amount;
        $discount = $record->discount_amount;
        $total = $record->total_amount;
        $originalTotal = $subtotal + $shipping + $tax;
        
        // Subtotal
        $content .= '<div class="flex justify-between py-1">';
        $content .= '<span class="text-sm text-gray-600 dark:text-gray-300">Subtotal:</span>';
        $content .= '<span class="text-sm font-medium">SAR ' . number_format($subtotal, 2) . '</span>';
        $content .= '</div>';
        
        // Shipping
        $content .= '<div class="flex justify-between py-1">';
        $content .= '<span class="text-sm text-gray-600 dark:text-gray-300">Shipping:</span>';
        $content .= '<span class="text-sm font-medium">SAR ' . number_format($shipping, 2) . '</span>';
        $content .= '</div>';
        
        // Tax
        $content .= '<div class="flex justify-between py-1">';
        $content .= '<span class="text-sm text-gray-600 dark:text-gray-300">Tax:</span>';
        $content .= '<span class="text-sm font-medium">SAR ' . number_format($tax, 2) . '</span>';
        $content .= '</div>';
        
        // Discount (if applicable)
        if ($discount > 0) {
            $content .= '<div class="flex justify-between py-2 mt-2 border-t border-gray-200 dark:border-gray-600">';
            $content .= '<span class="text-sm font-medium text-green-600">Discount:</span>';
            $content .= '<span class="text-sm font-bold text-green-600">-SAR ' . number_format($discount, 2) . '</span>';
            $content .= '</div>';
            
            $content .= '<div class="flex justify-between py-2 border-t border-gray-200 dark:border-gray-600">';
            $content .= '<span class="text-sm font-medium text-gray-600 dark:text-gray-300">Original Total:</span>';
            $content .= '<span class="text-sm font-bold">SAR ' . number_format($originalTotal, 2) . '</span>';
            $content .= '</div>';
        } else if ($record->coupon_id) {
            $content .= '<div class="flex justify-between py-2 mt-2 border-t border-gray-200 dark:border-gray-600">';
            $content .= '<span class="text-sm font-medium text-yellow-600">Discount:</span>';
            $content .= '<span class="text-sm font-bold text-yellow-600">SAR 0.00 (Coupon applied but no discount)</span>';
            $content .= '</div>';
        }
        
        // Final total
        $content .= '<div class="flex justify-between pt-3 mt-3 border-t border-gray-300 dark:border-gray-600">';
        $content .= '<span class="text-base font-bold text-gray-900 dark:text-white">Total:</span>';
        $content .= '<span class="text-base font-bold text-gray-900 dark:text-white">SAR ' . number_format($total, 2) . '</span>';
        $content .= '</div>';
        
        $content .= '</div>'; // end bg div
        $content .= '</div>'; // end order totals card
        
        $content .= '</div>'; // end p-4
        $content .= '</div>'; // end main container
        
        return $content;
    }
    
    public static function getOrderCalculationDetails($record): string
    {
        if (!$record) {
            return 'No order information';
        }
        
        $subtotal = $record->subtotal;
        $shipping = $record->shipping_cost;
        $tax = $record->tax_amount;
        $discount = $record->discount_amount;
        $total = $record->total_amount;
        $originalTotal = $subtotal + $shipping + $tax;
        
        $content = '<div class="space-y-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">';
        $content .= '<div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">';
        
        $content .= '<div class="flex justify-between">';
        $content .= '<span>Subtotal:</span>';
        $content .= '<span class="font-medium">SAR ' . number_format($subtotal, 2) . '</span>';
        $content .= '</div>';
        
        $content .= '<div class="flex justify-between">';
        $content .= '<span>Shipping:</span>';
        $content .= '<span class="font-medium">SAR ' . number_format($shipping, 2) . '</span>';
        $content .= '</div>';
        
        $content .= '<div class="flex justify-between">';
        $content .= '<span>Tax:</span>';
        $content .= '<span class="font-medium">SAR ' . number_format($tax, 2) . '</span>';
        $content .= '</div>';
        
        if ($discount > 0) {
            $content .= '<div class="flex justify-between text-green-600 border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">';
            $content .= '<span>Discount:</span>';
            $content .= '<span class="font-medium">-SAR ' . number_format($discount, 2) . '</span>';
            $content .= '</div>';
            
            $content .= '<div class="flex justify-between font-semibold border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">';
            $content .= '<span>Original Total:</span>';
            $content .= '<span>SAR ' . number_format($originalTotal, 2) . '</span>';
            $content .= '</div>';
        }
        
        $content .= '<div class="flex justify-between text-lg font-bold border-t border-gray-300 dark:border-gray-600 pt-2 mt-2">';
        $content .= '<span>Total:</span>';
        $content .= '<span>SAR ' . number_format($total, 2) . '</span>';
        $content .= '</div>';
        
        $content .= '</div>';
        $content .= '</div>';
        
        return $content;
    }
}