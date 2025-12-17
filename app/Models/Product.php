<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sku',
        'slug',
        'price',
        'tax_amount',
        'cost_price',
        'real_cost',
        'stock_quantity',
        'low_stock_threshold',
        'category_id',
        'image',
        'images',
        'brand',
        'supplier_type',
        'supplier_link',
        'is_featured',
        'is_active',
        'views_count',
        'average_rating',
        'colors',
        'sizes',
        'weight',
        'dimensions',
        'material',
        'warranty_months',
        'origin_country',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'brand' => 'json',
        'images' => 'json',
        'colors' => 'json',
        'sizes' => 'json',
        'price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = $product->generateUniqueSlug();
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !empty($product->getOriginal('name'))) {
                if (empty($product->slug)) {
                    $product->slug = $product->generateUniqueSlug();
                }
            }
        });
    }
    
    // Accessor to get the name based on the current locale
    public function getNameAttribute($value)
    {
        $names = json_decode($value, true);
        $locale = app()->getLocale();
        return $names[$locale] ?? $names['en'] ?? $names['ar'] ?? 'Unknown Product';
    }
    
    // Accessor to get the description based on the current locale
    public function getDescriptionAttribute($value)
    {
        $descriptions = json_decode($value, true);
        $locale = app()->getLocale();
        return $descriptions[$locale] ?? $descriptions['en'] ?? $descriptions['ar'] ?? '';
    }

    /**
     * Generate a unique slug for the product
     */
    public function generateUniqueSlug()
    {
        $name = is_array($this->name) ? 
            ($this->name['en'] ?? $this->name['ar'] ?? reset($this->name)) : 
            $this->name;
            
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * Check if the product has variants (colors or sizes)
     */
    public function hasVariants(): bool
    {
        // Check if colors array exists and has values
        $hasColors = !empty($this->colors) && is_array($this->colors) && count($this->colors) > 0;
        
        // Check if sizes array exists and has values
        $hasSizes = !empty($this->sizes) && is_array($this->sizes) && count($this->sizes) > 0;
        
        return $hasColors || $hasSizes;
    }
    
    /**
     * Get available colors for the product
     */
    public function getAvailableColors(): array
    {
        return $this->colors ?? [];
    }
    
    /**
     * Get available sizes for the product
     */
    public function getAvailableSizes(): array
    {
        return $this->sizes ?? [];
    }
}