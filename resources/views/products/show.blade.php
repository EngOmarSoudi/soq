@extends('layouts.page')

@section('title', (is_array($product->name) ? (app()->getLocale() === 'en' ? ($product->name['en'] ?? '') : ($product->name['ar'] ?? '')) : $product->name) . ' - EcommStore')

@section('page-title', is_array($product->name) ? (app()->getLocale() === 'en' ? ($product->name['en'] ?? '') : ($product->name['ar'] ?? '')) : $product->name)

@section('page-content')
<!-- Breadcrumb -->
<div class="mb-8 text-sm text-gray-600 dark:text-gray-400">
    <a href="{{ route('home') }}" class="hover:text-primary">{{ __('messages.home') }}</a>
    <span class="mx-2">/</span>
    <a href="{{ route('products.index') }}" class="hover:text-primary">{{ __('messages.products') }}</a>
    <span class="mx-2">/</span>
    <span>{{ is_array($product->name) ? (app()->getLocale() === 'en' ? ($product->name['en'] ?? '') : ($product->name['ar'] ?? '')) : $product->name }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
    <!-- Product Images -->
    <div>
        <div class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden mb-6 relative group">
            @php
                $allImages = collect([$product->image])->merge($product->images ?? [])->filter()->unique()->values(); // Re-index values
            @endphp
            
            @if($allImages->isNotEmpty())
                <img id="mainImage" src="{{ asset('storage/' . $allImages->first()) }}" 
                     data-index="0"
                     alt="{{ is_array($product->name) ? (app()->getLocale() === 'en' ? ($product->name['en'] ?? '') : ($product->name['ar'] ?? '')) : $product->name }}" 
                     class="w-full h-auto object-cover transition-opacity duration-300">
                
                @if($allImages->count() > 1)
                    <!-- Navigation Buttons -->
                    <button onclick="prevImage()" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button onclick="nextImage()" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                @endif
            @else
                <div class="w-full h-96 flex items-center justify-center text-6xl bg-gray-100 dark:bg-gray-700">
                    📦
                </div>
            @endif
        </div>
        
        <!-- Image Gallery Thumbnails -->
        @if($allImages->count() > 1)
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
                @foreach($allImages as $index => $image)
                    <button type="button" 
                            onclick="changeMainImage('{{ asset('storage/' . $image) }}', {{ $index }}, this)" 
                            data-index="{{ $index }}"
                            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 {{ $loop->first ? 'border-primary' : 'border-transparent' }} hover:border-primary transition-all gallery-thumbnail">
                        <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover" alt="Product thumbnail">
                    </button>
                @endforeach
            </div>
        @endif
        
        <!-- Product Badges -->
        <div class="flex gap-3 mt-4">
            @if($product->is_featured)
                <span class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold">
                    {{ __('messages.featured') }}
                </span>
            @endif
            @if($product->stock_quantity > 0)
                <span class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold">
                    {{ __('messages.in_stock') }}
                </span>
            @else
                <span class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold">
                    {{ __('messages.out_of_stock') }}
                </span>
            @endif
            
            <!-- Cart Indicator -->
            @auth
                @if($isInCart)
                    <span id="cart-indicator" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>{{ __('messages.in_your_cart') }} ({{ $cartQuantity }})</span>
                    </span>
                @else
                    <span id="cart-indicator" class="hidden bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>{{ __('messages.in_your_cart') }} (0)</span>
                    </span>
                @endif
            @endauth
        </div>
    </div>

    <!-- Product Details -->
    <div>
        <!-- SKU and Brand -->
        <div class="flex gap-4 mb-4 text-gray-600 dark:text-gray-400">
            <span>{{ __('messages.sku') }}: <strong>{{ $product->sku }}</strong></span>
            @if($product->brand)
                <span>{{ __('messages.brand') }}: <strong>{{ is_array($product->brand) ? (app()->getLocale() === 'en' ? ($product->brand['en'] ?? '') : ($product->brand['ar'] ?? '')) : $product->brand }}</strong></span>
            @endif
        </div>

        <!-- Rating -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex text-yellow-400 text-xl">
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
            <span class="text-gray-600 dark:text-gray-400">({{ number_format($product->average_rating, 1) }})</span>
        </div>

        <!-- Price -->
        <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-baseline gap-3">
                <span class="text-4xl font-bold text-primary">SAR {{ number_format($product->price, 2) }}</span>
                @if($product->cost_price)
                    <span class="text-lg text-gray-500 line-through">SAR {{ number_format($product->cost_price, 2) }}</span>
                @endif
            </div>
            
            <!-- Supplier Information for Online Products -->
            @if($product->supplier_type === 'online' && $product->supplier_link)
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        <span class="font-semibold text-blue-800 dark:text-blue-200">{{ __('messages.online_product') }}</span>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        {{ __('messages.online_product_description') }}
                    </p>
                    <a href="{{ $product->supplier_link }}" target="_blank" rel="noopener noreferrer" 
                       class="inline-flex items-center gap-1 mt-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        {{ __('messages.view_on_aliexpress') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Add to Cart Form -->
        @auth
            <form id="addToCartForm" method="POST" action="{{ route('cart.add') }}" class="mb-8">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                <!-- Variant Selection -->
                @if($product->hasVariants())
                    @php
                        $availableColors = $product->getAvailableColors();
                        $availableSizes = $product->getAvailableSizes();
                        $currentLocale = app()->getLocale();
                    @endphp
                    
                    @if(is_array($availableColors) && !empty($availableColors))
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2">{{ __('messages.color') }}</label>
                            <div class="flex flex-wrap gap-2" id="color-options">
                                @if(isset($availableColors[$currentLocale]) && is_array($availableColors[$currentLocale]))
                                    @foreach($availableColors[$currentLocale] as $color)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="color" value="{{ $color }}" class="sr-only color-option" required>
                                            <span class="inline-block px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary transition-colors">
                                                {{ $color }}
                                            </span>
                                        </label>
                                    @endforeach
                                @elseif(isset($availableColors['en']) && is_array($availableColors['en']))
                                    @foreach($availableColors['en'] as $color)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="color" value="{{ $color }}" class="sr-only color-option" required>
                                            <span class="inline-block px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary transition-colors">
                                                {{ $color }}
                                            </span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if(is_array($availableSizes) && !empty($availableSizes))
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2">{{ __('messages.size') }}</label>
                            <div class="flex flex-wrap gap-2" id="size-options">
                                @if(isset($availableSizes[$currentLocale]) && is_array($availableSizes[$currentLocale]))
                                    @foreach($availableSizes[$currentLocale] as $size)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="size" value="{{ $size }}" class="sr-only size-option" required>
                                            <span class="inline-block px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary transition-colors">
                                                {{ $size }}
                                            </span>
                                        </label>
                                    @endforeach
                                @elseif(isset($availableSizes['en']) && is_array($availableSizes['en']))
                                    @foreach($availableSizes['en'] as $size)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="size" value="{{ $size }}" class="sr-only size-option" required>
                                            <span class="inline-block px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-primary transition-colors">
                                                {{ $size }}
                                            </span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
                
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">{{ __('messages.quantity') }}</label>
                    <div class="flex items-center gap-3">
                        <input type="number" id="quantityInput" name="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-20 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.available') }}: {{ $product->stock_quantity }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" {{ $product->stock_quantity == 0 ? 'disabled' : '' }} class="flex-1 btn-primary text-white font-bold py-3 px-4 rounded-lg transition">
                        {{ __('messages.add_to_cart') }}
                    </button>
                </div>
            </form>
            <button type="button" onclick="toggleWishlist({{ $product->id }})" class="px-6 py-3 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-lg transition w-full mt-3">
                {{ __('messages.add_to_wishlist') }}
            </button>
        @else
            <div class="mb-8 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-100">
                    {{ __('messages.please_login_wishlist') }} <a href="{{ route('login') }}" class="font-semibold hover:underline">{{ __('messages.login') }}</a>
                </p>
            </div>
        @endauth

        <!-- Share Buttons -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-3">{{ __('messages.share_product') }}</h3>
            <div class="flex gap-3">
                <a href="#" class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                    <span class="sr-only">Facebook</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                    </svg>
                </a>
                <a href="#" class="w-10 h-10 flex items-center justify-center bg-sky-500 text-white rounded-full hover:bg-sky-600 transition">
                    <span class="sr-only">Twitter</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Product Tabs -->
<div class="card bg-white dark:bg-gray-800 rounded-xl p-6 mb-8">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex space-x-8">
            <button data-tab="description" class="tab-button py-4 px-1 border-b-2 border-primary text-primary font-medium">
                {{ __('messages.description') }}
            </button>
            <button data-tab="specifications" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                {{ __('messages.specifications') }}
            </button>
            <button data-tab="reviews" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                {{ __('messages.reviews') }} ({{ $reviews->count() }})
            </button>
        </nav>
    </div>
    
    <div class="py-6">
        <!-- Description Tab -->
        <div id="description-tab" class="tab-content">
            <div class="prose max-w-none dark:prose-invert">
                {!! is_array($product->description) ? (app()->getLocale() === 'en' ? ($product->description['en'] ?? '') : ($product->description['ar'] ?? '')) : $product->description !!}
            </div>
        </div>
        
        <!-- Specifications Tab -->
        <div id="specifications-tab" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold mb-3">{{ __('messages.general_info') }}</h4>
                    <ul class="space-y-2">
                        <li class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('messages.weight') }}</span>
                            <span>{{ $product->weight }} {{ __('messages.kg') }}</span>
                        </li>
                        <li class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('messages.dimensions') }}</span>
                            <span>{{ $product->dimensions }}</span>
                        </li>
                        <li class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('messages.material') }}</span>
                            <span>{{ is_array($product->material) ? (app()->getLocale() === 'en' ? ($product->material['en'] ?? '') : ($product->material['ar'] ?? '')) : $product->material }}</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">{{ __('messages.additional_info') }}</h4>
                    <ul class="space-y-2">
                        <li class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('messages.warranty') }}</span>
                            <span>{{ $product->warranty_months }} {{ __('messages.months') }}</span>
                        </li>
                        <li class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('messages.origin') }}</span>
                            <span>{{ is_array($product->origin_country) ? (app()->getLocale() === 'en' ? ($product->origin_country['en'] ?? '') : ($product->origin_country['ar'] ?? '')) : $product->origin_country }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Reviews Tab -->
        <div id="reviews-tab" class="tab-content hidden">
            @if($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-6 last:border-0 last:pb-0">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="font-bold">{{ substr($review->user->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-semibold">{{ $review->user->name }}</h4>
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <span>★</span>
                                                @else
                                                    <span class="text-gray-300">★</span>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ $review->created_at->format('M d, Y') }}</p>
                                    <p>{{ $review->comment }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_reviews_yet') }}</p>
            @endif
            
            @auth
                <div class="mt-8">
                    @if($userReview)
                        <!-- Edit Existing Review Form -->
                        <h4 class="font-semibold mb-4">{{ __('messages.edit_your_review') }}</h4>
                        @if($userReview->status === 'pending')
                            <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-yellow-800 dark:text-yellow-200 text-sm">
                                {{ __('messages.review_pending_approval') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('reviews.update', $userReview->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">{{ __('messages.rating') }}</label>
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400 text-2xl" id="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="cursor-pointer star" data-value="{{ $i }}">{{ $i <= $userReview->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="rating-input" value="{{ $userReview->rating }}" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="comment" class="block text-sm font-medium mb-2">{{ __('messages.comment') }}</label>
                                <textarea name="comment" id="comment" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>{{ $userReview->comment }}</textarea>
                            </div>
                            <button type="submit" class="btn-primary text-white px-6 py-2 rounded-lg font-semibold">
                                {{ __('messages.update_review') }}
                            </button>
                        </form>
                    @else
                        <!-- New Review Form -->
                        <h4 class="font-semibold mb-4">{{ __('messages.write_review') }}</h4>
                        <form id="reviewForm" method="POST" action="{{ route('reviews.store') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">{{ __('messages.rating') }}</label>
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400 text-2xl" id="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="cursor-pointer star" data-value="{{ $i }}">☆</span>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="rating-input" value="0" required>
                                </div>
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="comment" class="block text-sm font-medium mb-2">{{ __('messages.comment') }}</label>
                                <textarea name="comment" id="comment" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('comment') border-red-500 @enderror" required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="btn-primary text-white px-6 py-2 rounded-lg font-semibold">
                                {{ __('messages.submit_review') }}
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-100">
                        {{ __('messages.please_login_review') }} <a href="{{ route('login') }}" class="font-semibold hover:underline">{{ __('messages.login') }}</a>
                    </p>
                </div>
            @endauth
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image Gallery Functionality
    const productImages = @json($allImages->values()); // Use values to ensure array
    
    function changeMainImage(src, index, element) {
        const mainImage = document.getElementById('mainImage');
        
        // Update main image source with fade effect
        mainImage.style.opacity = '0.5';
        
        setTimeout(() => {
            mainImage.src = src;
            mainImage.dataset.index = index;
            mainImage.style.opacity = '1';
        }, 150);
        
        // Update active thumbnail
        document.querySelectorAll('.gallery-thumbnail').forEach(thumb => {
            thumb.classList.remove('border-primary');
            thumb.classList.add('border-transparent');
        });
        
        if (element) {
            element.classList.remove('border-transparent');
            element.classList.add('border-primary');
        } else {
             // Find by index if element not passed
            const thumb = document.querySelector(`.gallery-thumbnail[data-index="${index}"]`);
            if (thumb) {
                thumb.classList.remove('border-transparent');
                thumb.classList.add('border-primary');
            }
        }
    }
    
    function nextImage() {
        const mainImage = document.getElementById('mainImage');
        let currentIndex = parseInt(mainImage.dataset.index || 0);
        let nextIndex = currentIndex + 1;
        
        if (nextIndex >= productImages.length) {
            nextIndex = 0;
        }
        
        const nextSrc = '{{ asset("storage") }}/' + productImages[nextIndex];
        changeMainImage(nextSrc, nextIndex, null);
    }
    
    function prevImage() {
        const mainImage = document.getElementById('mainImage');
        let currentIndex = parseInt(mainImage.dataset.index || 0);
        let prevIndex = currentIndex - 1;
        
        if (prevIndex < 0) {
            prevIndex = productImages.length - 1;
        }
        
        const prevSrc = '{{ asset("storage") }}/' + productImages[prevIndex];
        changeMainImage(prevSrc, prevIndex, null);
    }

    // Tab switching functionality
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons and tabs
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('text-primary', 'border-primary');
                btn.classList.add('text-gray-500', 'border-transparent');
            });
            
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Add active class to clicked button
            this.classList.remove('text-gray-500', 'border-transparent');
            this.classList.add('text-primary', 'border-primary');
            
            // Show corresponding tab
            const tabId = this.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.remove('hidden');
        });
    });
    
    // Star rating functionality
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            document.getElementById('rating-input').value = value;
            
            // Update star display
            document.querySelectorAll('.star').forEach((s, index) => {
                if (index < value) {
                    s.textContent = '★';
                } else {
                    s.textContent = '☆';
                }
            });
        });
    });
    
    // Wishlist toggle functionality
    function toggleWishlist(productId) {
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
                showToast(data.action === 'added' ? '{{ __("messages.added_to_wishlist") }}' : '{{ __("messages.removed_from_wishlist") }}', 'success');
            } else {
                showToast('{{ __("messages.error_occurred") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ __("messages.error_occurred") }}', 'error');
        });
    }
    
    // Add to cart form submission
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count in header
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
                
                // Show cart indicator badge and update quantity
                const cartIndicator = document.querySelector('#cart-indicator');
                if (cartIndicator) {
                    cartIndicator.classList.remove('hidden');
                    // Update the quantity text
                    const quantitySpan = cartIndicator.querySelector('span');
                    if (quantitySpan) {
                        // Extract the base text (everything before the parentheses)
                        const baseText = quantitySpan.textContent.split('(')[0].trim();
                        // Update with the new quantity
                        quantitySpan.textContent = `${baseText} (${data.product_quantity})`;
                    }
                }
                
                showToast('{{ __("messages.product_added_to_cart") }}', 'success');
                
                // Reset form
                this.reset();
            } else {
                showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ __("messages.error_occurred") }}', 'error');
        });
    });


    // Review Form Validation
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            const rating = document.getElementById('rating-input').value;
            const comment = document.getElementById('comment').value;
            let hasError = false;
            
            if (rating < 1) {
                showToast("{{ __('messages.rating_field_is_required') }}", 'error');
                hasError = true;
            }
            
            if (comment.length < 10) {
                showToast("{{ __('messages.comment_min_length') }}", 'error');
                hasError = true;
            }
            
            if (hasError) {
                e.preventDefault();
            }
        });
    }
</script>
@endpush
@endsection