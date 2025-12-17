<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Services\AliExpressService;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Basic Information
                TextInput::make('name')
                    ->label('Product Name (will display based on locale)')
                    ->helperText('Enter product name - the system will use the appropriate language based on user locale')
                    ->required(),
                
                TextInput::make('description')
                    ->label('Product Description (will display based on locale)')
                    ->helperText('Enter product description')
                    ->columnSpanFull(),
                
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->unique(ignoreRecord: true),
                
                TextInput::make('slug')
                    ->helperText('Will be automatically generated if left empty')
                    ->unique(ignoreRecord: true),
                
                // Pricing & Stock
                TextInput::make('price')
                    ->numeric()
                    ->required(),
                
                TextInput::make('tax_amount')
                    ->numeric()
                    ->default(0.00)
                    ->helperText('Custom tax amount for this product (in SAR)'),
                
                TextInput::make('cost_price')
                    ->label('Product Price Before Discount')
                    ->helperText('The original price shown as strikethrough')
                    ->numeric(),
                
                TextInput::make('real_cost')
                    ->label('Real Cost Price')
                    ->helperText('Actual cost of the product (for internal analytics)')
                    ->numeric(),
                
                TextInput::make('stock_quantity')
                    ->numeric()
                    ->default(0),
                
                TextInput::make('low_stock_threshold')
                    ->numeric()
                    ->default(10),
                
                // Media
                FileUpload::make('image')
                    ->image()
                    ->directory('products')
                    ->disk('public'),
                
                FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->directory('products')
                    ->disk('public'),
                
                // Additional Details
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                
                TextInput::make('brand')
                    ->label('Brand (will display based on locale)')
                    ->helperText('Enter brand name'),
                
                // Variants
                Repeater::make('colors')
                    ->label('Available Colors')
                    ->schema([
                        TextInput::make('en')
                            ->label('English')
                            ->required(),
                        TextInput::make('ar')
                            ->label('Arabic')
                            ->required(),
                    ])
                    ->defaultItems(0)
                    ->collapsible()
                    ->cloneable()
                    ->mutateDehydratedStateUsing(function ($state) {
                        if (is_array($state)) {
                            $processed = [];
                            foreach ($state as $item) {
                                if (isset($item['en']) && isset($item['ar'])) {
                                    $processed['en'][] = $item['en'];
                                    $processed['ar'][] = $item['ar'];
                                }
                            }
                            return $processed;
                        }
                        return $state;
                    })
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && isset($state['en']) && isset($state['ar'])) {
                            $processed = [];
                            $count = max(count($state['en']), count($state['ar']));
                            for ($i = 0; $i < $count; $i++) {
                                $processed[] = [
                                    'en' => $state['en'][$i] ?? '',
                                    'ar' => $state['ar'][$i] ?? '',
                                ];
                            }
                            return $processed;
                        }
                        return [];
                    }),
                
                Repeater::make('sizes')
                    ->label('Available Sizes')
                    ->schema([
                        TextInput::make('en')
                            ->label('English')
                            ->required(),
                        TextInput::make('ar')
                            ->label('Arabic')
                            ->required(),
                    ])
                    ->defaultItems(0)
                    ->collapsible()
                    ->cloneable()
                    ->mutateDehydratedStateUsing(function ($state) {
                        if (is_array($state)) {
                            $processed = [];
                            foreach ($state as $item) {
                                if (isset($item['en']) && isset($item['ar'])) {
                                    $processed['en'][] = $item['en'];
                                    $processed['ar'][] = $item['ar'];
                                }
                            }
                            return $processed;
                        }
                        return $state;
                    })
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && isset($state['en']) && isset($state['ar'])) {
                            $processed = [];
                            $count = max(count($state['en']), count($state['ar']));
                            for ($i = 0; $i < $count; $i++) {
                                $processed[] = [
                                    'en' => $state['en'][$i] ?? '',
                                    'ar' => $state['ar'][$i] ?? '',
                                ];
                            }
                            return $processed;
                        }
                        return [];
                    }),
                
                Select::make('supplier_type')
                    ->options([
                        'local' => 'Local Supplier',
                        'online' => 'Online Supplier',
                    ])
                    ->default('local')
                    ->live(),
                
                TextInput::make('supplier_link')
                    ->url()
                    ->helperText('External supplier link')
                    ->visible(fn ($get) => $get('supplier_type') === 'online'),
                
                Toggle::make('is_featured')
                    ->default(false),
                
                Toggle::make('is_active')
                    ->default(true),
                
                // Product Specifications
                TextInput::make('weight')
                    ->label('Weight (kg)')
                    ->numeric()
                    ->suffix('kg'),
                
                TextInput::make('dimensions')
                    ->label('Dimensions')
                    ->placeholder('e.g., 10x20x30 cm'),
                
                TextInput::make('material')
                    ->label('Material'),
                
                TextInput::make('origin_country')
                    ->label('Origin Country'),
                
                TextInput::make('warranty_months')
                    ->label('Warranty (Months)')
                    ->numeric()
                    ->suffix('months'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record->name['en'] ?? $record->name['ar'] ?? 'N/A')
                    ->sortable(),
                
                TextColumn::make('sku')
                    ->sortable(),
                
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => 'SAR ' . number_format($state, 2))
                    ->sortable(),
                
                TextColumn::make('stock_quantity')
                    ->sortable(),

                TextColumn::make('sales_count')
                    ->label('Total Sales')
                    ->state(function (Product $record): int {
                        return $record->orderItems()->sum('quantity');
                    })
                    ->sortable(query: function ($query, string $direction): \Illuminate\Database\Eloquent\Builder {
                        return $query->withCount('orderItems as sales_count')
                            ->orderBy('sales_count', $direction);
                    }),
                
                IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('supplier_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'local' => 'success',
                        'online' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->actions([
                // Temporarily removed actions due to missing class error
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}