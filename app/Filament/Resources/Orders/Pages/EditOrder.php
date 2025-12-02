<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function mount($record): void
    {
        parent::mount($record);
        
        // Ensure relationships are loaded
        $this->record->load(['items.product', 'user', 'shippingAddress', 'billingAddress', 'coupon']);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
