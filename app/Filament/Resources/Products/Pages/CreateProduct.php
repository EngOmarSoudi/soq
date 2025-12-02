<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Category;
use App\Services\AliExpressService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_from_aliexpress')
                ->label('Import from AliExpress')
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    TextInput::make('aliexpress_url')
                        ->label('AliExpress Product URL')
                        ->url()
                        ->required()
                        ->helperText('Paste the full AliExpress product URL'),
                    Select::make('target_category')
                        ->label('Category')
                        ->options(Category::all()->pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        // Create an instance of AliExpressService
                        $aliExpressService = new AliExpressService();
                        
                        // Fetch product details from AliExpress
                        $productData = $aliExpressService->fetchProductDetails($data['aliexpress_url']);
                        
                        // Prepare data for the form
                        $formData = [
                            'name' => $productData['name'],
                            'description' => $productData['description'],
                            'price' => $productData['price'],
                            'category_id' => $data['target_category'],
                            'supplier_type' => 'online',
                            'supplier_link' => $data['aliexpress_url'],
                        ];
                        
                        // Set colors and sizes if available
                        if (!empty($productData['attributes']['color'])) {
                            $formData['colors'] = [];
                            foreach ($productData['attributes']['color'] as $color) {
                                $formData['colors'][] = [
                                    'en' => $color,
                                    'ar' => $color // In a real implementation, you would translate these
                                ];
                            }
                        }
                        
                        if (!empty($productData['attributes']['size'])) {
                            $formData['sizes'] = [];
                            foreach ($productData['attributes']['size'] as $size) {
                                $formData['sizes'][] = [
                                    'en' => $size,
                                    'ar' => $size // In a real implementation, you would translate these
                                ];
                            }
                        }
                        
                        // Set the form data
                        $this->form->fill($formData);
                        
                        // Show success notification
                        Notification::make()
                            ->title('AliExpress Import Successful')
                            ->body('Product data has been imported successfully. Please review and save the product.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        // Show error notification
                        Notification::make()
                            ->title('Import Failed')
                            ->body('Failed to import product: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Import Product from AliExpress')
                ->modalDescription('This will fetch product data from the provided AliExpress URL and populate the form fields.')
                ->modalSubmitActionLabel('Import Product'),
        ];
    }
}