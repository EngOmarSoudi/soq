<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                KeyValue::make('name')
                    ->label('Category Name')
                    ->keyLabel('Language')
                    ->valueLabel('Name')
                    ->required(),
                
                KeyValue::make('description')
                    ->label('Description')
                    ->keyLabel('Language')
                    ->valueLabel('Description'),
                
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                
                FileUpload::make('image')
                    ->image()
                    ->directory('categories'),
                
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->getStateUsing(fn($record) => $record->name['en'] ?? $record->name['ar'] ?? 'N/A')
                    ->sortable(),
                
                TextColumn::make('slug')
                    ->sortable(),
                
                TextColumn::make('sort_order')
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->actions([
                // Temporarily removed actions due to missing class error
            ])
            ->bulkActions([
                // Remove bulk actions for now to keep it simple
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}