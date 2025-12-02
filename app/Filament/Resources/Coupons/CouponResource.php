<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Basic Information
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true),
                
                KeyValue::make('name')
                    ->label('Coupon Name')
                    ->keyLabel('Language')
                    ->valueLabel('Name')
                    ->required(),
                
                KeyValue::make('description')
                    ->label('Description')
                    ->keyLabel('Language')
                    ->valueLabel('Description'),
                
                // Discount Settings
                Select::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage (%)',
                        'fixed' => 'Fixed Amount ($)',
                    ])
                    ->required(),
                
                TextInput::make('discount_value')
                    ->numeric()
                    ->required(),
                
                TextInput::make('minimum_order_amount')
                    ->numeric()
                    ->helperText('Minimum order amount to apply coupon'),
                
                TextInput::make('maximum_discount')
                    ->numeric()
                    ->helperText('Maximum discount amount'),
                
                // Usage Limits
                TextInput::make('usage_limit')
                    ->numeric()
                    ->helperText('Total number of times coupon can be used'),
                
                TextInput::make('per_user_limit')
                    ->numeric()
                    ->default(1)
                    ->helperText('Maximum times per user'),
                
                // Validity
                DateTimePicker::make('valid_from')
                    ->required(),
                
                DateTimePicker::make('valid_until'),
                
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record->name['en'] ?? $record->name['ar'] ?? 'N/A')
                    ->sortable(),
                
                TextColumn::make('discount_value')
                    ->getStateUsing(fn($record) => $record->discount_value . ($record->discount_type === 'percentage' ? '%' : '$'))
                    ->sortable(),
                
                TextColumn::make('usage_count')
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('valid_until')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                // Temporarily removed actions due to missing class error
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}