<div>
    @if($getState() && $getState()->product)
        @php
            $product = $getState()->product;
        @endphp
        <div class="space-y-2">
            <div class="text-sm">
                <span class="font-medium">Supplier Type:</span> 
                <span class="capitalize">{{ $product->supplier_type ?? 'N/A' }}</span>
            </div>
            
            @if($product->supplier_type === 'online' && $product->supplier_link)
                <div class="text-sm">
                    <span class="font-medium">Product Link:</span>
                    <a href="{{ $product->supplier_link }}" target="_blank" 
                       class="text-primary hover:underline inline-flex items-center gap-1">
                        View Product
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            @elseif($product->supplier_type === 'local')
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Local supplier - Product managed internally
                </div>
            @endif
        </div>
    @else
        <div class="text-gray-500 dark:text-gray-400">No supplier information</div>
    @endif
</div>