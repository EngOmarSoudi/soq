<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'site_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // General Settings
                TextInput::make('site_name')
                    ->label('Site Name')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('site_email')
                    ->label('Contact Email')
                    ->email()
                    ->maxLength(255),
                
                TextInput::make('site_phone')
                    ->label('Contact Phone')
                    ->tel()
                    ->maxLength(255),
                
                TextInput::make('site_address')
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
                
                // Social Media
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site_name')
                    ->label('Site Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('site_email')
                    ->label('Email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('site_phone')
                    ->label('Phone')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
