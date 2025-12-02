<?php

namespace App\Filament\Resources\ExternalProductResource\Pages;

use App\Filament\Resources\ExternalProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExternalProducts extends ListRecords
{
    protected static string $resource = ExternalProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}