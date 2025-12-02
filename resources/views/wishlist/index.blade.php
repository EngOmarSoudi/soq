@extends('layouts.page')

@section('title', __('messages.my_wishlist') . ' - EcommStore')

@section('page-title', __('messages.my_wishlist'))

@section('page-content')
@if($wishlists->isEmpty())
    <div class="card bg-white dark:bg-gray-800 rounded-xl p-8 text-center max-w-2xl mx-auto">
        <div class="mb-6">
            <svg class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">{{ __('messages.wishlist_empty') }}</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.add_products_wishlist') }}</p>
        <a href="{{ route('products.index') }}" class="inline-block btn-primary text-white font-semibold py-3 px-8 rounded-lg transition">
            {{ __('messages.continue_shopping') }}
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @forelse($wishlists as $wishlist)
            <div class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden flex flex-col h-full group" data-wishlist-id="{{ $wishlist->id }}">
                <div class="relative bg-gray-100 dark:bg-gray-700 h-48 overflow-hidden">
                    @if($wishlist->product->image)
                        <img src="{{ asset('storage/' . $wishlist->product->image) }}" alt="{{ $wishlist->product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl">
                            📦
                        </div>
                    @endif
                    
                    <!-- Remove Button -->
                    <button type="button" onclick="removeFromWishlist({{ $wishlist->id }})" class="absolute top-3 right-3 bg-white dark:bg-gray-800 p-2 rounded-full hover:bg-red-100 dark:hover:bg-red-900 transition shadow-lg">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    
                    <!-- Featured Badge -->
                    @if($wishlist->product->is_featured)
                        <div class="absolute top-3 left-3 bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-xs font-semibold">
                            {{ __('messages.featured') }}
                        </div>
                    @endif
                </div>
                
                <div class="p-4 flex-1 flex flex-col">
                    <a href="{{ route('products.show', $wishlist->product->slug) }}" class="font-semibold text-lg mb-2 line-clamp-2 hover:text-primary transition">
                        {{ $wishlist->product->name }}
                    </a>
                    
                    <!-- Rating -->
                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($wishlist->product->average_rating))
                                    <span>★</span>
                                @elseif($i - $wishlist->product->average_rating < 1)
                                    <span>★</span>
                                @else
                                    <span class="text-gray-300">★</span>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-gray-600 dark:text-gray-400 ml-2">({{ $wishlist->product->reviews_count ?? 0 }})</span>
                    </div>
                    
                    <!-- Stock Status -->
                    @if($wishlist->product->stock_quantity > 0)
                        <span class="text-xs text-green-600 dark:text-green-400 font-semibold mb-3">{{ __('messages.in_stock') }}</span>
                    @else
                        <span class="text-xs text-red-600 dark:text-red-400 font-semibold mb-3">{{ __('messages.out_of_stock') }}</span>
                    @endif
                    
                    <div class="mt-auto flex items-center justify-between">
                        <span class="text-2xl font-bold text-primary">${{ number_format($wishlist->product->price, 2) }}</span>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 dark:bg-gray-700 space-y-3">
                    <a href="{{ route('products.show', $wishlist->product->slug) }}" class="w-full text-center btn-primary text-white font-semibold py-2 rounded-lg transition block">
                        {{ __('messages.view_details') }}
                    </a>
                    <button type="button" onclick="addToCart({{ $wishlist->product->id }})" {{ $wishlist->product->stock_quantity == 0 ? 'disabled' : '' }} class="w-full text-center border-2 border-primary text-primary hover:bg-primary hover:text-white disabled:border-gray-400 disabled:text-gray-400 font-semibold py-2 rounded-lg transition">
                        {{ __('messages.add_to_cart') }}
                    </button>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-600 dark:text-gray-400">{{ __('messages.no_products') }}</p>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($wishlists->hasPages())
        <div class="mb-8">
            {{ $wishlists->links() }}
        </div>
    @endif
@endif

@push('scripts')
<script>
    // Prevent duplicate clicks
    let isProcessing = false;
    
    function removeFromWishlist(wishlistId) {
        if (isProcessing) return;
        
        if (confirm('{{ __("messages.confirm_remove") }}')) {
            isProcessing = true;
            
            fetch(`/wishlist/${wishlistId}/remove`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Product removed from wishlist', 'success');
                    // Remove the item from the DOM instead of full page reload
                    document.querySelector(`[data-wishlist-id="${wishlistId}"]`).remove();
                } else {
                    showToast(data.message || 'Error removing product', 'error');
                }
                isProcessing = false;
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error removing product', 'error');
                isProcessing = false;
            });
        }
    }
    
    function addToCart(productId) {
        if (isProcessing) return;
        
        const quantity = 1; // Default quantity
        isProcessing = true;
        
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count in header
                if (data.cart_count !== undefined) {
                    updateCartCount(data.cart_count);
                }
                showToast(data.message || 'Product added to cart', 'success');
            } else {
                showToast(data.message || 'Error adding to cart', 'error');
            }
            isProcessing = false;
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error adding to cart', 'error');
            isProcessing = false;
        });
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? '✓' : '✕';
        
        toast.innerHTML = `
            <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
                <span class="text-xl font-bold">${icon}</span>
                <span>${message}</span>
            </div>
        `;
        
        toast.className = 'fixed top-4 right-4 z-50 animate-slide-in';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endpush
@endsection