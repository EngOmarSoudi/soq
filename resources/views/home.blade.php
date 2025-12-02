@extends('layouts.app')

@section('title', __('messages.home') . ' - EcommStore')

@push('styles')
<style>
    /* Floating Cards Effect */
    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.8);
    }
    
    .dark .card {
        background-color: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Background Animation Elements -->
<div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
    <div class="absolute top-0 left-0 w-96 h-96 bg-gradient-to-r from-primary to-secondary rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-gradient-to-r from-blue-500 to-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
</div>

<!-- Featured Products Section -->
<section id="products" class="pt-16 pb-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center section-title mx-auto mb-12">
            <h1 class="text-4xl font-bold mb-4">{{ __('messages.featured_products') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                {{ __('messages.discover_handpicked') }}
            </p>
        </div>
        
        <!-- Advanced Filters -->
        <div class="card bg-gray-50 dark:bg-gray-800 rounded-xl p-6 mb-8">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4" method="GET">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('messages.search_products') }}</label>
                    <input type="text" name="search" placeholder="{{ __('messages.search') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('messages.category') }}</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">{{ __('messages.all_categories') }}</option>
                        @forelse($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ app()->getLocale() === 'en' ? $category->name['en'] ?? '' : $category->name['ar'] ?? '' }}
                            </option>
                        @empty
                        @endforelse
                    </select>
                </div>
                
                <!-- Price Range -->
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('messages.price_range') }}</label>
                    <select name="price_range" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">{{ __('messages.all_prices') }}</option>
                        <option value="0-50">$0 - $50</option>
                        <option value="50-100">$50 - $100</option>
                        <option value="100-500">$100 - $500</option>
                        <option value="500+">$500+</option>
                    </select>
                </div>
                
                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium mb-2">{{ __('messages.sort_by') }}</label>
                    <select name="sort" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="latest">{{ __('messages.latest') }}</option>
                        <option value="price-low">{{ __('messages.price_low_to_high') }}</option>
                        <option value="price-high">{{ __('messages.price_high_to_low') }}</option>
                        <option value="rating">{{ __('messages.highest_rated') }}</option>
                    </select>
                </div>
                <button type="submit" class="hidden"></button>
            </form>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($products as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden flex flex-col h-full group no-underline">
                    <!-- Product Image -->
                    <div class="relative bg-gray-100 dark:bg-gray-700 h-48 overflow-hidden">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name['en'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl">
                                📦
                            </div>
                        @endif
                        
                        <!-- Badge -->
                        @if($product->is_featured)
                            <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                {{ __('messages.featured') }}
                            </div>
                        @endif
                        
                        <!-- Stock Status -->
                        <div class="absolute bottom-3 left-3">
                            @if($product->stock_quantity > 0)
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ __('messages.in_stock') }}
                                </span>
                            @else
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ __('messages.out_of_stock') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-semibold text-lg mb-2 line-clamp-2 group-hover:text-primary transition text-gray-900 dark:text-white">
                            {{ app()->getLocale() === 'en' ? $product->name['en'] ?? '' : $product->name['ar'] ?? '' }}
                        </h3>
                        
                        <!-- Rating -->
                        <div class="flex items-center mb-3">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($product->average_rating))
                                        <span>★</span>
                                    @elseif($i - $product->average_rating < 1)
                                        <span>★</span>
                                    @else
                                        <span class="text-gray-300">★</span>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ $product->reviews()->count() }})</span>
                        </div>
                        
                        <!-- Description -->
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                            {{ app()->getLocale() === 'en' ? ($product->description['en'] ?? '') : ($product->description['ar'] ?? '') }}
                        </p>
                        
                        <!-- Price -->
                        <div class="mt-auto mb-4">
                            <span class="text-2xl font-bold text-primary">SAR {{ number_format($product->price, 2) }}</span>
                            @if($product->cost_price)
                                <span class="text-sm text-gray-500 line-through ml-2">SAR {{ number_format($product->cost_price, 2) }}</span>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button class="flex-1 border-2 border-primary text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-lg font-semibold transition wishlist-btn" data-product-id="{{ $product->id }}" title="{{ __('messages.add_to_wishlist') }}">
                                ♥
                            </button>
                            <!-- Add to Cart Button -->
                            <button class="flex-1 border-2 border-primary text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-lg font-semibold transition add-to-cart-btn" data-product-id="{{ $product->id }}" title="{{ __('messages.add_to_cart') }}">
                                {{ __('messages.add_to_cart') }}
                            </button>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_products_available') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="py-16 relative overflow-hidden">
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center section-title mx-auto mb-12">
            <h2 class="text-4xl font-bold mb-4">{{ __('messages.shop_by_category') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                {{ __('messages.browse_wide_selection') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($categories as $category)
                <a href="{{ route('products.category', $category->slug) }}" class="group">
                    <div class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden h-full shadow-lg group-hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col">
                        @if($category->image)
                            <div class="relative bg-gray-100 dark:bg-gray-700 h-48 overflow-hidden">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ app()->getLocale() === 'en' ? $category->name['en'] ?? '' : $category->name['ar'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            </div>
                        @else
                            <div class="relative bg-gradient-to-br from-primary to-secondary h-48 flex items-center justify-center text-4xl">
                                <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                                <span class="relative text-white">📦</span>
                            </div>
                        @endif
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="text-lg font-semibold mb-2 group-hover:text-primary transition">
                                {{ app()->getLocale() === 'en' ? $category->name['en'] ?? '' : $category->name['ar'] ?? '' }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 flex-1">
                                {{ app()->getLocale() === 'en' ? ($category->description['en'] ?? '') : ($category->description['ar'] ?? '') }}
                            </p>
                            <div class="mt-4">
                                <span class="inline-block btn-primary text-white px-4 py-2 rounded-lg font-semibold text-sm transition hover:shadow-lg">
                                    {{ __('messages.view_products') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_categories_available') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-primary to-secondary text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-4">{{ __('messages.ready_to_shop') }}</h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            {{ __('messages.explore_thousands') }}
        </p>
        <a href="{{ route('products.index') }}" class="inline-block btn-primary text-white px-8 py-4 rounded-lg font-bold text-lg transition whitespace-nowrap shadow-lg hover:shadow-xl">
            {{ __('messages.browse_products') }}
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Wishlist functionality
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            
            if (!isAuthenticated) {
                alert('{{ __('messages.please_login_wishlist') }}');
                window.location.href = '{{ route('login') }}';
                return;
            }
            
            // Toggle wishlist
            fetch(`/api/wishlist/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    this.style.color = data.added ? '#ef4444' : '#3b82f6';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    
    // Add to Cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const button = this;
            
            // Show loading state
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Adding...</span>';
            button.disabled = true;
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    updateCartCount(data.cart_count);
                    
                    // Show success toast
                    showToast(data.message || '{{ __("messages.product_added_to_cart") }}', 'success');
                } else {
                    showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('{{ __("messages.error_occurred") }}', 'error');
            })
            .finally(() => {
                // Restore button state
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    });
    
    // Filter form submission
    const filterForm = document.getElementById('filterForm');
    
    // Auto-submit on filter change (with debounce)
    let filterTimeout;
    filterForm.addEventListener('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            this.submit();
        }, 500); // 500ms delay to prevent excessive submissions
    });
</script>
@endpush