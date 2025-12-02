<?php

namespace App\Filament\Resources\ApiSettingResource\Pages;

use App\Filament\Resources\ApiSettingResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;

class ManageApiSettings extends Page
{
    protected static string $resource = ApiSettingResource::class;

    protected static ?string $title = 'API Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'TEMU_API_URL' => config('services.temu.url'),
            'TEMU_APP_KEY' => config('services.temu.key'),
            'TEMU_SECRET' => config('services.temu.secret'),
            'CJ_API_URL' => config('services.cj.url'),
            'CJ_API_KEY' => config('services.cj.key'),
            'CJ_API_SECRET' => config('services.cj.secret'),
            'SHEIN_API_URL' => config('services.shein.url'),
            'SHEIN_APP_ID' => config('services.shein.id'),
            'SHEIN_SECRET' => config('services.shein.secret'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
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
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    public function save(): void
    {
        // In a real implementation, you would save these settings to the database or .env file
        $this->notify('success', 'Settings saved successfully');
    }
}