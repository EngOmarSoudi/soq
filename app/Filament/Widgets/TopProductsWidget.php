<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopProductsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->withCount('orderItems as total_sales')->orderBy('total_sales', 'desc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->getStateUsing(fn(Product $record) => is_array($record->name) ? ($record->name[app()->getLocale()] ?? $record->name['en'] ?? '') : $record->name),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Sales')
                    ->sortable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Total Views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable(),
            ]);
    }
}
