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
                                                {{ __('messags.size') }}: {{ $item->size }}
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
                                    <button type="button" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-l-lg decrement-btn disabled:opacity-50" data-item-id="{{ $item->id }}" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                    <input type="number" min="1" max="{{ $item->product->stock_quantity }}" value="{{ $item->quantity }}" data-original-value="{{ $item->quantity }}" class="quantity-input w-12 h-8 border-y border-gray-300 dark:border-gray-600 text-center focus:outline-none focus:ring-2 focus:ring-primary hide-arrows" data-item-id="{{ $item->id }}">
                                    <button type="button" class="w-8 h-8 flex items-center justify-center border border-gray-300 dark:border-gray-600 rounded-r-lg increment-btn" data-item-id="{{ $item->id }}" {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>+</button>
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
                
                <a href="{{ route('cart.checkout') }}" class="w-full btn-primary text-white font-bold py-3 px-4 rounded-lg transition text-center block" onclick="proceedToCheckout(event)">
                    {{ __('messages.proceed_to_checkout') }}
                </a>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    // Track pending updates with timestamps to handle rapid updates
    window.pendingUpdates = new Map();
    
    // Function to generate unique request IDs
    function generateRequestId() {
        return Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }
    
    // Function to wait for all pending updates to complete
    function waitForPendingUpdates() {
        return new Promise((resolve, reject) => {
            let timeoutCounter = 0;
            const maxTimeout = 30; // 30 seconds max wait
            
            const checkPending = () => {
                if (window.pendingUpdates.size === 0) {
                    console.log('All pending updates completed');
                    resolve();
                } else {
                    console.log('Still waiting for', window.pendingUpdates.size, 'pending updates');
                    timeoutCounter++;
                    
                    // Log details of pending updates
                    console.log('Pending updates details:', Array.from(window.pendingUpdates.entries()));
                    
                    if (timeoutCounter >= maxTimeout) {
                        console.warn('Timeout waiting for pending updates, proceeding anyway');
                        resolve(); // Proceed anyway to avoid blocking forever
                    } else {
                        setTimeout(checkPending, 1000); // Check every second
                    }
                }
            };
            checkPending();
        });
    }
    
    // Modified checkout function that waits for pending updates
    function proceedToCheckout(event) {
        console.log('Proceeding to checkout, pending updates:', window.pendingUpdates.size);
        
        // Log all pending updates for debugging
        if (window.pendingUpdates.size > 0) {
            console.log('Pending updates details:', Array.from(window.pendingUpdates.entries()));
        }
        
        // Prevent default action
        event.preventDefault();
        
        // Prevent multiple clicks
        const checkoutLink = event.target.closest('a');
        if (checkoutLink.dataset.checkoutProcessing === 'true') {
            console.log('Checkout already in progress');
            return;
        }
        
        // Mark as processing
        checkoutLink.dataset.checkoutProcessing = 'true';
        checkoutLink.style.opacity = '0.7';
        
        // Wait for all pending updates to complete
        waitForPendingUpdates().then(() => {
            console.log('All pending updates completed, proceeding to checkout');
            // Proceed with the checkout
            window.location.href = event.target.href || event.target.closest('a').href;
        }).catch(error => {
            console.error('Error waiting for pending updates:', error);
            // Reset processing state on error
            checkoutLink.dataset.checkoutProcessing = 'false';
            checkoutLink.style.opacity = '1';
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing cart page');
    // Store original values for error recovery
    document.querySelectorAll('.quantity-input').forEach(input => {
        // Ensure the input value is synchronized with the displayed value
        input.dataset.originalValue = input.value;
        // Track if there's an ongoing update request
        input.dataset.updating = 'false';
        // Track the last value that was successfully saved (initialize with current value)
        input.dataset.lastSavedValue = input.value;
        
        console.log('Initialized input for item', input.getAttribute('data-item-id'), 'with value', input.value);
        
        // Ensure the displayed value matches the stored value
        // NOTE: We don't need to set input.value here since it's already correct
        // input.value = input.dataset.lastSavedValue;
    });
    
    // Quantity input validation
    document.querySelectorAll('.quantity-input').forEach(input => {
        console.log('Adding event listeners for input', input.getAttribute('data-item-id'));
        // Add event listeners
        input.addEventListener('change', handleQuantityChange);
        
        // Handle input validation on blur
        input.addEventListener('blur', function() {
            console.log('Blur event for input', this.getAttribute('data-item-id'), 'value:', this.value);
            // Don't revert if there's an ongoing update request
            if (this.dataset.updating === 'true') {
                console.log('Skipping blur handler - update in progress');
                return;
            }
            
            // Get the item ID from the input
            const itemId = this.getAttribute('data-item-id');
            if (!itemId) {
                console.error('Could not find item ID for quantity input');
                return;
            }
            
            // Parse current and last saved values
            const currentValue = parseInt(this.value) || 0;
            const lastSavedValue = parseInt(this.dataset.lastSavedValue) || 1;
            
            console.log('Current value:', currentValue, 'Last saved value:', lastSavedValue);
            
            // Only revert if the value is invalid (less than 1)
            if (currentValue < 1) {
                console.log('Reverting invalid value to last saved value');
                this.value = lastSavedValue;
                // Revert the item total
                const itemContainer = document.querySelector(`[data-item-id="${itemId}"]`);
                const itemTotalElement = itemContainer ? itemContainer.querySelector('.item-total') : null;
                if (itemTotalElement) {
                    const price = parseFloat(itemTotalElement.dataset.price) || 0;
                    itemTotalElement.textContent = 'SAR ' + (lastSavedValue * price).toFixed(2);
                    // Update order summary again
                    updateOrderSummary();
                }
            } else {
                // If the value is valid and different from the last saved value,
                // update the last saved value to reflect the user's input
                if (currentValue !== lastSavedValue) {
                    console.log('Updating last saved value to current value');
                    this.dataset.lastSavedValue = currentValue;
                }
            }
        });
        
        // Also handle the case when user presses Enter key
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleQuantityChange.call(this);
                this.blur(); // Trigger blur to finalize the change
            }
        });
    });
    
    // Handle increment button clicks
    document.querySelectorAll('.increment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            console.log('Increment button clicked for item', itemId);
            const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
            if (input) {
                let currentValue = parseInt(input.value) || 1;
                let maxValue = parseInt(input.max) || Infinity;
                let newValue = Math.min(currentValue + 1, maxValue);
                console.log('Incrementing from', currentValue, 'to', newValue);
                updateQuantity(itemId, newValue, false);
            }
        });
    });
    
    // Handle decrement button clicks
    document.querySelectorAll('.decrement-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            console.log('Decrement button clicked for item', itemId);
            const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
            if (input) {
                let currentValue = parseInt(input.value) || 1;
                let newValue = Math.max(currentValue - 1, 1);
                console.log('Decrementing from', currentValue, 'to', newValue);
                updateQuantity(itemId, newValue, false);
            }
        });
    });
    
    function handleQuantityChange() {
        console.log('Handle quantity change for input', this.getAttribute('data-item-id'), 'value:', this.value);
        
        const inputField = this;
        const itemContainer = inputField.closest('[data-item-id]');
        // Safely get the item ID
        const itemId = itemContainer ? itemContainer.getAttribute('data-item-id') : null;
        if (!itemId) {
            console.error('Could not find item ID for quantity input');
            return;
        }
        
        const itemTotalElement = itemContainer.querySelector('.item-total');
        if (!itemTotalElement) {
            console.error('Could not find item total element');
            return;
        }
        
        const price = parseFloat(itemTotalElement.dataset.price) || 0;
        const maxValue = parseInt(this.max) || Infinity;
        
        // Ensure value is within valid range
        let value = parseInt(this.value) || 1;
        if (value < 1) value = 1;
        if (value > maxValue) value = maxValue;
        
        console.log('Processed value:', value, 'Price:', price, 'Max value:', maxValue);
        
        // Only update the input value if it's actually different to avoid
        // interfering with the change event
        if (parseInt(this.value) !== value) {
            this.value = value;
        }
        
        // IMMEDIATELY update the last saved value to prevent revert on blur
        // This is crucial for preventing the revert issue on subsequent changes
        // Ensure we're storing a valid number
        this.dataset.lastSavedValue = parseInt(value) || 1;
        
        // Update button states
        const container = this.closest('.flex.items-center');
        const minusButton = container ? container.querySelector('.decrement-btn') : null;
        const plusButton = container ? container.querySelector('.increment-btn') : null;
        
        if (minusButton) {
            minusButton.disabled = value <= 1;
        }
        if (plusButton) {
            plusButton.disabled = value >= maxValue;
        }
        
        // Update quantity on server (UI will be updated with server response)
        updateQuantity(itemId, value, false);
    }
});

function updateOrderSummary() {
    let subtotal = 0;
    
    document.querySelectorAll('.item-total').forEach(element => {
        // Extract the numeric value from the text content
        const priceText = element.textContent.replace('SAR ', '').replace(/[^0-9.-]+/g, '');
        const priceValue = parseFloat(priceText);
        console.log('Item total element:', element.textContent, 'Parsed value:', priceValue);
        if (!isNaN(priceValue)) {
            subtotal += priceValue;
        }
    });
    
    console.log('Calculated subtotal:', subtotal);
    
    // Use the tax amount passed from the server
    const shippingCost = {{ $shippingCost ?? 10.00 }};
    const tax = {{ $taxAmount }}; // Use the correct tax amount from the server
    const total = subtotal + shippingCost + tax;
    
    console.log('Shipping:', shippingCost, 'Tax:', tax, 'Total:', total);
    
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
    
    console.log('Updating quantity for item', itemId, 'to', quantity);
    
    // Generate a unique request ID for this update
    const requestId = generateRequestId();
    
    // Track this update as pending with the request ID
    window.pendingUpdates.set(requestId, { itemId: itemId, timestamp: Date.now() });
    
    // Get the input element to check max value
    const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
    if (input) {
        // Mark as updating to prevent blur handler from reverting changes
        input.dataset.updating = 'true';
        
        const maxValue = parseInt(input.max) || Infinity;
        if (quantity > maxValue) quantity = maxValue;
        
        // Update input value immediately for better UX
        // Only update if different to avoid interfering with change event
        if (parseInt(input.value) !== quantity) {
            input.value = quantity;
        }
        
        // IMMEDIATELY update the last saved value to prevent revert on blur
        // This is crucial for preventing the revert issue on subsequent changes
        // Ensure we're storing a valid number
        input.dataset.lastSavedValue = parseInt(quantity) || 1;
        
        // Update item total display immediately
        const itemContainer = document.querySelector(`[data-item-id="${itemId}"]`);
        const itemTotalElement = itemContainer ? itemContainer.querySelector('.item-total') : null;
        if (itemTotalElement) {
            const price = parseFloat(itemTotalElement.dataset.price) || 0;
            const newText = 'SAR ' + (quantity * price).toFixed(2);
            console.log('Updating item total for item', itemId, 'from', itemTotalElement.textContent, 'to', newText);
            itemTotalElement.textContent = newText;
            // Update order summary immediately
            console.log('Updating order summary after item total change');
            updateOrderSummary();
        }
        
        // Update button states
        const container = input.closest('.flex.items-center');
        const minusButton = container ? container.querySelector('.decrement-btn') : null;
        const plusButton = container ? container.querySelector('.increment-btn') : null;
        
        if (minusButton) {
            minusButton.disabled = quantity <= 1;
        }
        if (plusButton) {
            plusButton.disabled = quantity >= maxValue;
        }
    }
    
    const formData = new FormData();
    formData.append('quantity', quantity);
    
    console.log('Sending update request for item', itemId, 'with quantity', quantity);
    
    fetch(`/cart/update/${itemId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: formData
    })
    .then(response => {
        console.log('Received response for item', itemId, response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Received data for item', itemId, data);
        // Remove from pending updates
        // Find and remove the request by checking itemId
        for (const [key, value] of window.pendingUpdates.entries()) {
            if (value.itemId === itemId) {
                window.pendingUpdates.delete(key);
                break;
            }
        }
        
        // Reset updating flag
        if (input) {
            input.dataset.updating = 'false';
        }
        
        if (data.success) {
            console.log('Server response successful for item', itemId, data);
            // NOTE: lastSavedValue is already updated immediately when we change the input
            // So we don't need to update it here anymore
            
            // Update the item total display with server value if provided
            const itemContainer = document.querySelector(`[data-item-id="${itemId}"]`);
            const itemTotalElement = itemContainer ? itemContainer.querySelector('.item-total') : null;
            if (itemTotalElement && data.item_total) {
                console.log('Updating item total with server value:', data.item_total);
                itemTotalElement.textContent = 'SAR ' + data.item_total;
                // Update order summary with the server-provided value
                updateOrderSummary();
            }
            
            // Update cart count in header if provided
            if (data.cart_count !== undefined) {
                updateCartCount(data.cart_count);
            }
            
            if (showToast) {
                showToast('{{ __("messages.cart_updated") }}', 'success');
            }
            
            // Update order summary is already done when we updated the item total
            // updateOrderSummary();
        } else {
            console.log('Server response failed for item', itemId, data);
            // Reset updating flag
            if (input) {
                input.dataset.updating = 'false';
            }
            
            // Show error message
            if (showToast) {
                showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
            }
            
            // Revert to last saved value on error
            if (input) {
                input.value = input.dataset.lastSavedValue || '1';
                
                // Revert button states
                const container = input.closest('.flex.items-center');
                const minusButton = container ? container.querySelector('.decrement-btn') : null;
                const plusButton = container ? container.querySelector('.increment-btn') : null;
                
                if (minusButton) {
                    minusButton.disabled = parseInt(input.dataset.lastSavedValue || '1') <= 1;
                }
                if (plusButton) {
                    const maxVal = parseInt(input.max) || Infinity;
                    plusButton.disabled = parseInt(input.dataset.lastSavedValue || '1') >= maxVal;
                }
                
                // Revert the item total
                const itemContainer = document.querySelector(`[data-item-id="${itemId}"]`);
                const itemTotalElement = itemContainer ? itemContainer.querySelector('.item-total') : null;
                if (itemTotalElement) {
                    const price = parseFloat(itemTotalElement.dataset.price) || 0;
                    const originalValue = parseInt(input.dataset.lastSavedValue || '1');
                    itemTotalElement.textContent = 'SAR ' + (originalValue * price).toFixed(2);
                    // Update order summary again
                    updateOrderSummary();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error updating quantity for item', itemId, error);
        // Remove from pending updates
        // Find and remove the request by checking itemId
        for (const [key, value] of window.pendingUpdates.entries()) {
            if (value.itemId === itemId) {
                window.pendingUpdates.delete(key);
                break;
            }
        }
        
        // Reset updating flag
        if (input) {
            input.dataset.updating = 'false';
        }
        
        // Revert to last saved value on error
        if (input) {
            input.value = input.dataset.lastSavedValue || '1';
            
            // Revert button states
            const container = input.closest('.flex.items-center');
            const minusButton = container ? container.querySelector('.decrement-btn') : null;
            const plusButton = container ? container.querySelector('.increment-btn') : null;
            
            if (minusButton) {
                minusButton.disabled = parseInt(input.dataset.lastSavedValue || '1') <= 1;
            }
            if (plusButton) {
                const maxVal = parseInt(input.max) || Infinity;
                plusButton.disabled = parseInt(input.dataset.lastSavedValue || '1') >= maxVal;
            }
            
            // Revert the item total
            const itemContainer = document.querySelector(`[data-item-id="${itemId}"]`);
            const itemTotalElement = itemContainer ? itemContainer.querySelector('.item-total') : null;
            if (itemTotalElement) {
                const price = parseFloat(itemTotalElement.dataset.price) || 0;
                const originalValue = parseInt(input.dataset.lastSavedValue || '1');
                itemTotalElement.textContent = 'SAR ' + (originalValue * price).toFixed(2);
                // Update order summary again
                updateOrderSummary();
            }
        }
        
        if (showToast) {
            showToast('{{ __("messages.error_occurred") }}: ' + error.message, 'error');
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
            
            // Update cart count in header
            if (data.cart_count !== undefined) {
                updateCartCount(data.cart_count);
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

@push('styles')
<style>
/* Hide spinners/arrows for number inputs */
.hide-arrows::-webkit-outer-spin-button,
.hide-arrows::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.hide-arrows {
  -moz-appearance: textfield;
}
</style>
@endpush