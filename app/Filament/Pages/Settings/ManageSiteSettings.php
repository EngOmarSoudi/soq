<?php

namespace App\Filament\Pages\Settings;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Set;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.settings.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::getSetting();
        $this->fill(['data' => $settings->toArray()]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('site_name')
                    ->label('Site Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                
                TextInput::make('site_email')
                    ->label('Contact Email')
                    ->email()
                    ->maxLength(255),
                
                TextInput::make('site_phone')
                    ->label('Contact Phone')
                    ->tel()
                    ->maxLength(255),
                
               Textarea::make('site_address')
                    ->label('Physical Address')
                    ->helperText('The address displayed in the footer.')
                    ->rows(3)
                    ->columnSpanFull(),
                
                TextInput::make('site_address_map_url')
                    ->label('Address Map Link (Optional)')
                    ->url()
                    ->helperText('If provided, the address will link to this URL. If empty, it will auto-generate a Google Maps search link for the address above.')
                    ->columnSpanFull(),
                
                Textarea::make('site_description')
                    ->label('Site Description')
                    ->rows(3)
                    ->columnSpanFull(),
                
                TextInput::make('facebook_url')
                    ->label('Facebook URL')
                    ->url()
                    ->maxLength(255),
                
                TextInput::make('twitter_url')
                    ->label('Twitter URL')
                    ->url()
                    ->maxLength(255),
                
                TextInput::make('instagram_url')
                    ->label('Instagram URL')
                    ->url()
                    ->maxLength(255),
                
                TextInput::make('linkedin_url')
                    ->label('LinkedIn URL')
                    ->url()
                    ->maxLength(255),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $settings = SiteSetting::getSetting();
            $settings->update($this->data);
            
            // Clear cache
            Cache::forget('site_settings');
            
            Notification::make()
                ->title('Settings updated successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to update settings')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Site Settings';
    }

    public static function getBreadcrumb(): string
    {
        return 'Site Settings';
    }
}