<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CheckProductTax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:check-tax {slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tax settings for a product by slug';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        $product = Product::where('slug', $slug)->first();
        
        if (!$product) {
            $this->error("Product with slug '{$slug}' not found");
            return;
        }
        
        $this->info("Product: " . ($product->name['en'] ?? $product->name['ar'] ?? 'N/A'));
        $this->info("Slug: " . $product->slug);
        $this->info("Price: SAR " . number_format($product->price, 2));
        $this->info("Tax Amount: " . $product->tax_amount . "%");
        $this->info("Calculated Tax: SAR " . number_format($product->price * ($product->tax_amount / 100), 2));
    }
}