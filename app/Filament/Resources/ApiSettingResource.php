<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiSettingResource\Pages;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiSettingResource extends Resource
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = 'External Platforms';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('API Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temu')
                            ->schema([
                                Forms\Components\TextInput::make('TEMU_API_URL')
                                    ->label('API URL')
                                    ->default('https://api.temu.com')
                                    ->required(),
                                Forms\Components\TextInput::make('TEMU_APP_KEY')
                                    ->label('App Key')
                                    ->required(),
                                Forms\Components\TextInput::make('TEMU_SECRET')
                                    ->label('Secret')
                                    ->password()
                                    ->required(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('test_temu')
                                        ->label('Test Connection')
                                        ->action(function () {
                                            // Test Temu API connection
                                        })
                                        ->color('primary'),
                                ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('CJ Dropshipping')
                            ->schema([
                                Forms\Components\TextInput::make('CJ_API_URL')
                                    ->label('API URL')
                                    ->default('https://developers.cjdropshipping.com/api')
                                    ->required(),
                                Forms\Components\TextInput::make('CJ_API_KEY')
                                    ->label('API Key')
                                    ->required(),
                                Forms\Components\TextInput::make('CJ_API_SECRET')
                                    ->label('API Secret')
                                    ->password()
                                    ->required(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('test_cj')
                                        ->label('Test Connection')
                                        ->action(function () {
                                            // Test CJ API connection
                                        })
                                        ->color('primary'),
                                ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Shein')
                            ->schema([
                                Forms\Components\TextInput::make('SHEIN_API_URL')
                                    ->label('API URL')
                                    ->default('https://openapi.shein.com')
                                    ->required(),
                                Forms\Components\TextInput::make('SHEIN_APP_ID')
                                    ->label('App ID')
                                    ->required(),
                                Forms\Components\TextInput::make('SHEIN_SECRET')
                                    ->label('Secret')
                                    ->password()
                                    ->required(),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('test_shein')
                                        ->label('Test Connection')
                                        ->action(function () {
                                            // Test Shein API connection
                                        })
                                        ->color('primary'),
                                ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageApiSettings::route('/'),
        ];
    }
}