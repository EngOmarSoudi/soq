<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bank_name')
                    ->required(),
                TextInput::make('account_name')
                    ->required(),
                TextInput::make('account_number')
                    ->required(),
                TextInput::make('routing_number'),
                TextInput::make('swift_code'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
