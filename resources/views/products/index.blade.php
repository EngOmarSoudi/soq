@extends('layouts.page')

@section('title', (isset($category) ? $category->name : __('messages.all_products')) . ' - EcommStore')

@section('page-title', isset($category) ? $category->name : __('messages.all_products'))

@section('page-description', isset($category) ? $category->description : __('messages.discover_handpicked'))

@section('page-content')
@push('styles')
<style>
    /* Enhanced Filter Styles */
    .filter-card {
        transition: all 0.3s ease;
    }
    
    .filter-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Custom select styling */
    select {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Focus states for better accessibility */
    input:focus, select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .filter-card {
            padding: 1.5rem;
        }
        
        .filter-grid {
            gap: 1rem;
        }
    }
</style>
@endpush

<!-- Enhanced Filters Section -->
<div class="filter-card bg-white dark:bg-gray-800 rounded-xl p-6 mb-8 shadow-lg">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.filters') }}</h2>
        <button type="button" id="clearFilters" class="text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition">
            {{ __('messages.clear_all') }}
        </button>
    </div>
    
    <form id="filterForm" class="filter-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" method="GET">
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.search_products') }}</label>
            <div class="relative">
                <input type="text" name="search" placeholder="{{ __('messages.search') }}" class="w-full px-4 py-3 pl-10 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition" value="{{ request('search') }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Category Filter -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.category') }}</label>
            <select name="category" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                <option value="">{{ __('messages.all_categories') }}</option>
                @forelse($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @empty
                @endforelse
            </select>
        </div>
        
        <!-- Price Range -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.price_range') }}</label>
            <select name="price_range" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                <option value="">{{ __('messages.all_prices') }}</option>
                <option value="0-50" {{ request('price_range') == '0-50' ? 'selected' : '' }}>$0 - $50</option>
                <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>$50 - $100</option>
                <option value="100-500" {{ request('price_range') == '100-500' ? 'selected' : '' }}>$100 - $500</option>
                <option value="500+" {{ request('price_range') == '500+' ? 'selected' : '' }}>$500+</option>
            </select>
        </div>
        
        <!-- Color Filter -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.color') }}</label>
            <select name="color" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                <option value="">{{ __('messages.all_colors') }}</option>
                @if(isset($availableColors))
                    @foreach($availableColors as $color)
                        <option value="{{ $color }}" {{ request('color') == $color ? 'selected' : '' }}>{{ $color }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        
        <!-- Size Filter -->
        <div>
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.size') }}</label>
            <select name="size" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                <option value="">{{ __('messages.all_sizes') }}</option>
                @if(isset($availableSizes))
                    @foreach($availableSizes as $size)
                        <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        
        <!-- Sort -->
        <div class="md:col-span-2 lg:col-span-3 xl:col-span-6">
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.sort_by') }}</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select name="sort" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('messages.latest') }}</option>
                    <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>{{ __('messages.price_low_to_high') }}</option>
                    <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>{{ __('messages.price_high_to_low') }}</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('messages.highest_rated') }}</option>
                </select>
                <button type="submit" class="px-6 py-3 btn-primary text-white rounded-lg font-semibold transition hover:shadow-lg">
                    {{ __('messages.apply_filters') }}
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Products Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    @forelse($products as $product)
        <a href="{{ route('products.show', $product->slug) }}" class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden flex flex-col h-full group no-underline">
            <!-- Product Image -->
            <div class="relative bg-gray-100 dark:bg-gray-700 h-48 overflow-hidden">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
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
                    {{ $product->name }}
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
                    {{ $product->description }}
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

<!-- Pagination -->
@if($products->hasPages())
    <div class="flex justify-center">
        {{ $products->links() }}
    </div>
@endif

@push('scripts')
<script>
    // Filter form submission
    const filterForm = document.getElementById('filterForm');
    
    // Add transition effect when applying filters
    filterForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<span class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...</span>';
            submitBtn.disabled = true;
        }
    });
    
    // Auto-submit on filter change (with debounce)
    let filterTimeout;
    filterForm.addEventListener('change', function() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500); // 500ms delay to prevent excessive submissions
    });
    
    // Clear filters button
    document.getElementById('clearFilters').addEventListener('click', function() {
        // Get current URL
        const url = new URL(window.location);
        
        // Remove filter parameters
        url.searchParams.delete('search');
        url.searchParams.delete('category');
        url.searchParams.delete('price_range');
        url.searchParams.delete('color');
        url.searchParams.delete('size');
        
        // Keep sort parameter if it exists
        // Redirect to the clean URL
        window.location.href = url.toString();
    });
    
    // Wishlist functionality
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const button = this;
            
            fetch('{{ url("api/wishlist") }}/' + productId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show appropriate message based on whether product was added or already exists
                    showToast(data.message, data.added ? 'success' : 'info');
                    
                    // Update button state only if product was added
                    if (data.added) {
                        button.innerHTML = '♥';
                        button.classList.add('bg-primary', 'text-white', 'border-primary');
                    }
                } else {
                    showToast('{{ __("messages.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('{{ __("messages.error_occurred") }}', 'error');
            });
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
                    // Update cart count in header using global function
                    if (data.cart_count !== undefined) {
                        updateCartCount(data.cart_count);
                    }
                    
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
</script>
@endpush
@endsection