<?php

namespace App\Filament\Resources\ApiLogResource\Pages;

use App\Filament\Resources\ApiLogResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\File;

class ViewApiLog extends ViewRecord
{
    protected static string $resource = ApiLogResource::class;

    public function mount($record): void
    {
        parent::mount($record);
        
        // Load log content
        $logPath = storage_path('logs/' . $record);
        if (File::exists($logPath)) {
            $this->data['content'] = File::get($logPath);
        } else {
            $this->data['content'] = 'Log file not found.';
        }
    }
}