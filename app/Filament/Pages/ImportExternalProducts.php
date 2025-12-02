<?php

namespace App\Filament\Pages;

use App\Services\API\CJService;
use App\Services\API\SheinService;
use App\Services\API\TemuService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ImportExternalProducts extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string | \UnitEnum | null $navigationGroup = 'External Platforms';

    protected static ?int $navigationSort = 0;

    // Removed $view property due to conflict with parent class

    public $platform = 'temu';
    public $keyword = '';
    public $categoryId = '';
    public $page = 1;
    public $limit = 20;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('platform')
                    ->label('Platform')
                    ->options([
                        'temu' => 'Temu',
                        'cj' => 'CJ Dropshipping',
                        'shein' => 'Shein',
                    ])
                    ->default('temu')
                    ->required()
                    ->live(),
                
                Forms\Components\TextInput::make('keyword')
                    ->label('Search Keyword')
                    ->placeholder('Enter keyword to search for products')
                    ->visible(fn ($get) => $get('platform') === 'cj'),
                
                Forms\Components\TextInput::make('categoryId')
                    ->label('Category ID')
                    ->placeholder('Enter category ID (optional)')
                    ->visible(fn ($get) => $get('platform') === 'shein'),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('page')
                            ->label('Page')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        
                        Forms\Components\TextInput::make('limit')
                            ->label('Products per Page')
                            ->numeric()
                            ->default(20)
                            ->minValue(1)
                            ->maxValue(100),
                    ]),
                
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('import')
                        ->label('Import Products')
                        ->action('importProducts')
                        ->color('primary'),
                ]),
            ])
            ->statePath('data');
    }

    public function importProducts(): void
    {
        try {
            $command = '';
            
            switch ($this->data['platform']) {
                case 'temu':
                    $command = "import:temu-products --page={$this->data['page']} --limit={$this->data['limit']}";
                    break;
                case 'cj':
                    $keyword = $this->data['keyword'] ?? 'electronics';
                    $command = "import:cj-products {$keyword} --page={$this->data['page']} --limit={$this->data['limit']}";
                    break;
                case 'shein':
                    $categoryId = $this->data['categoryId'] ?? '';
                    $command = "import:shein-products {$categoryId} --page={$this->data['page']} --limit={$this->data['limit']}";
                    break;
            }
            
            // Run the import command
            Artisan::call($command);
            
            $output = Artisan::output();
            
            $this->notify('success', 'Products imported successfully: ' . $output);
            
        } catch (\Exception $e) {
            Log::error('Product import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->notify('error', 'Product import failed: ' . $e->getMessage());
        }
    }
}