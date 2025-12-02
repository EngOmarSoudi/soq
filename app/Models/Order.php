<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_address_id',
        'billing_address_id',
        'subtotal',
        'shipping_cost',
        'tax_amount',
        'coupon_id',
        'discount_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'notes',
        'shipped_at',
        'delivered_at',
        'platform', // External platform name (temu, cj, shein)
        'platform_order_id', // Order ID on the external platform
        'tracking_number', // Tracking number for the shipment
        'tracking_url', // URL to track the shipment
        'shipping_status', // Shipping status from the platform
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Get the receipt URL if it exists
    public function getReceiptUrlAttribute()
    {
        if ($this->payment_reference) {
            return Storage::url($this->payment_reference);
        }
        return null;
    }
}