<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->visible(fn ($livewire) => $livewire instanceof CreateUser),
                
                TextInput::make('phone')
                    ->tel(),
                
                Select::make('role')
                    ->options([
                        'customer' => 'Customer',
                        'admin' => 'Admin',
                    ])
                    ->required(),
                
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('phone')
                    ->sortable(),
                
                TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'danger',
                        'customer' => 'info',
                    })
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'customer' => 'Customer',
                        'admin' => 'Admin',
                    ]),
                \Filament\Tables\Filters\Filter::make('recently_active')
                    ->label('Recently Active (30 Days)')
                    ->query(function ($query) {
                        $thirtyDaysAgo = now()->subDays(30);
                        return $query->where(function($q) use ($thirtyDaysAgo) {
                            $q->where('last_login_at', '>=', $thirtyDaysAgo)
                              ->orWhereHas('orders', function($subQ) use ($thirtyDaysAgo) {
                                  $subQ->where('created_at', '>=', $thirtyDaysAgo);
                              });
                        });
                    })
                    ->toggle(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}