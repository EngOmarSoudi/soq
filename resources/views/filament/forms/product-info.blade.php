<div>
    @if(isset($item) && $item && $item->product)
        @php
            $product = $item->product;
            $productName = is_array($product->name) ? 
                ($product->name[app()->getLocale()] ?? $product->name['en'] ?? reset($product->name)) : 
                $product->name;
        @endphp
        <div class="flex items-center gap-3">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $productName }}" class="w-12 h-12 object-cover rounded">
            @else
                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                    </svg>
                </div>
            @endif
            <div>
                <a href="/products/{{ $product->slug }}" target="_blank" class="font-medium text-primary hover:underline">{{ $productName }}</a>
                <div class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
            </div>
        </div>
    @else
        <div class="text-gray-500 dark:text-gray-400">No product information</div>
    @endif
</div>