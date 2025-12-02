@extends('layouts.page')

@section('title', __('messages.shopping_cart'))

@section('page-title', __('messages.shopping_cart'))

@section('page-content')
@if($cartItems->isEmpty())
    <div class="text-center py-12">
        <div class="text-6xl mb-4">🛒</div>
        <h2 class="text-2xl font-bold mb-2">{{ __('messages.your_cart_empty') }}</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.continue_shopping') }}</p>
        <a href="{{ route('products.index') }}" class="btn-primary text-white font-bold py-3 px-6 rounded-lg transition inline-block">
            {{ __('messages.continue_shopping') }}
        </a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                @foreach($cartItems as $item)
                    <div class="flex items-center p-6 border-b border-gray-200 dark:border-gray-700 last:border-b-0" data-item-id="{{ $item->id }}">
                        <div class="flex-shrink-0 w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                            @if($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ is_array($item->product->name) ? (app()->getLocale() === 'en' ? ($item->product->name['en'] ?? '') : ($item->product->name['ar'] ?? '')) : $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-2xl">
                                    📦
                                </div>
                            @endif
                        </div>
                        
                        <div class="ml-4 flex-1">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ is_array($item->product->name) ? (app()->getLocale() === 'en' ? ($item->product->name['en'] ?? '') : ($item->product->name['ar'] ?? '')) : $item->product->name }}
                                    </h3>
                                    @if($item->color || $item->size)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($item->color)
                                                {{ __('messages.color') }}: {{ $item->color }}
                                            @endif
                                            @if($item->size)
                                                @if($item->color), @endif
                                                {{ __('messages.size') }}: {{ $item->size }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <button type="button" onclick="removeFromCart({{ $item->id }})" class="text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <button type="button" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-l-lg disabled:opacity-50" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                    <input type="number" min="1" max="{{ $item->product->stock_quantity }}" value="{{ $item->quantity }}" data-original-value="{{ $item->quantity }}" class="quantity-input w-12 h-8 border-y border-gray-300 dark:border-gray-600 text-center focus:outline-none focus:ring-2 focus:ring-primary">
                                    <button type="button" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-r-lg" {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>+</button>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500 dark:text-gray-400 line-through text-sm" id="original-price-{{ $item->id }}">SAR {{ number_format($item->product->cost_price ?? $item->price, 2) }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white item-total" data-price="{{ $item->price }}">SAR {{ number_format($item->price * $item->quantity, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div>
            <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-4">{{ __('messages.order_summary') }}</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.subtotal') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white" data-summary="subtotal">SAR {{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.shipping') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white" data-summary="shipping">SAR {{ number_format($shippingCost ?? 10.00, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('messages.tax') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white" data-summary="tax">SAR {{ number_format($taxAmount, 2) }}</span>
                    </div>
                </div>
                
                <div class="flex justify-between text-lg font-bold mb-6 text-gray-900 dark:text-white">
                    <span>{{ __('messages.total') }}</span>
                    <span data-summary="total">SAR {{ number_format($total, 2) }}</span>
                </div>
                
                <a href="{{ route('cart.checkout') }}" class="w-full btn-primary text-white font-bold py-3 px-4 rounded-lg transition text-center block">
                    {{ __('messages.proceed_to_checkout') }}
                </a>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store original values for error recovery
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.dataset.originalValue = input.value;
    });
    
    // Quantity input validation
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.closest('[data-item-id]').dataset.itemId;
            const itemTotalElement = this.closest('[data-item-id]').querySelector('.item-total');
            const price = parseFloat(itemTotalElement.dataset.price);
            const maxValue = parseInt(this.max);
            
            // Ensure value is within valid range
            let value = parseInt(this.value) || 1;
            if (value < 1) value = 1;
            if (value > maxValue) value = maxValue;
            
            this.value = value;
            
            // Update item total
            itemTotalElement.textContent = 'SAR ' + (value * price).toFixed(2);
            
            // Update order summary
            updateOrderSummary();
            
            // Update quantity on server
            updateQuantity(itemId, value, false);
        });
        
        // Handle input validation on blur
        input.addEventListener('blur', function() {
            const originalValue = this.dataset.originalValue || this.value || 1;
            this.value = originalValue;
            // Revert the item total
            const itemTotalElement = this.closest('[data-item-id]').querySelector('.item-total');
            const price = parseFloat(itemTotalElement.dataset.price);
            itemTotalElement.textContent = 'SAR ' + (originalValue * price).toFixed(2);
            // Update order summary again
            updateOrderSummary();
        });
    });
});

function updateOrderSummary() {
    let subtotal = 0;
    
    document.querySelectorAll('.item-total').forEach(element => {
        const priceText = element.textContent.replace('SAR ', '').replace(/,/g, '');
        subtotal += parseFloat(priceText);
    });
    
    // Use the tax amount passed from the server
    const shippingCost = {{ $shippingCost ?? 10.00 }};
    const tax = {{ $taxAmount }}; // Use the correct tax amount from the server
    const total = subtotal + shippingCost + tax;
    
    // Update summary display (if it exists)
    const summaryElements = document.querySelectorAll('[data-summary]');
    summaryElements.forEach(el => {
        if (el.getAttribute('data-summary') === 'subtotal') {
            el.textContent = 'SAR ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else if (el.getAttribute('data-summary') === 'tax') {
            el.textContent = 'SAR ' + tax.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else if (el.getAttribute('data-summary') === 'shipping') {
            el.textContent = 'SAR ' + shippingCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else if (el.getAttribute('data-summary') === 'total') {
            el.textContent = 'SAR ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    });
}

function updateQuantity(itemId, quantity, showToast = true) {
    if (quantity < 1) return;
    
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('quantity', quantity);
    
    fetch(`/cart/update/${itemId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the original value
            const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
            if (input) {
                input.dataset.originalValue = quantity;
            }
            
            if (showToast) {
                showToast('{{ __("messages.cart_updated") }}', 'success');
            }
            
            // Update order summary
            updateOrderSummary();
        } else {
            // Revert to original value on error
            const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
            if (input) {
                input.value = input.dataset.originalValue;
                // Revert the item total
                const itemTotalElement = input.closest('[data-item-id]').querySelector('.item-total');
                const price = parseFloat(itemTotalElement.dataset.price);
                const originalValue = parseInt(input.dataset.originalValue);
                itemTotalElement.textContent = 'SAR ' + (originalValue * price).toFixed(2);
                // Update order summary again
                updateOrderSummary();
            }
            
            if (showToast) {
                showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert to original value on error
        const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
        if (input) {
            input.value = input.dataset.originalValue;
            // Revert the item total
            const itemTotalElement = input.closest('[data-item-id]').querySelector('.item-total');
            const price = parseFloat(itemTotalElement.dataset.price);
            const originalValue = parseInt(input.dataset.originalValue);
            itemTotalElement.textContent = 'SAR ' + (originalValue * price).toFixed(2);
            // Update order summary again
            updateOrderSummary();
        }
        
        if (showToast) {
            showToast('{{ __("messages.error_occurred") }}', 'error');
        }
    });
}

function removeFromCart(itemId) {
    if (!confirm('{{ __("messages.confirm_remove_item") }}')) {
        return;
    }
    
    fetch(`/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from the DOM
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                itemElement.remove();
            }
            
            // Update order summary
            updateOrderSummary();
            
            showToast('{{ __("messages.item_removed") }}', 'success');
            
            // If cart is empty, reload the page to show empty state
            if (document.querySelectorAll('[data-item-id]').length === 0) {
                location.reload();
            }
        } else {
            showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('{{ __("messages.error_occurred") }}', 'error');
    });
}
</script>
@endpush
@endsection