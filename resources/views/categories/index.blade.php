@extends('layouts.page')

@section('title', __('messages.categories') . ' - EcommStore')

@section('page-title', __('messages.categories'))

@section('page-description', __('messages.browse_all_categories'))

@section('page-content')
<!-- Enhanced Filters Section -->
<div class="filter-card bg-white dark:bg-gray-800 rounded-xl p-6 mb-8 shadow-lg">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.categories') }}</h2>
        <button type="button" id="clearFilters" class="text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg font-medium transition">
            {{ __('messages.clear_all') }}
        </button>
    </div>
    
    <form id="filterForm" class="filter-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" method="GET">
        <!-- Search -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.search_categories') }}</label>
            <div class="relative">
                <input type="text" name="search" placeholder="{{ __('messages.search') }}" class="w-full px-4 py-3 pl-10 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition" value="{{ request('search') }}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Sort -->
        <div class="md:col-span-2 lg:col-span-3 xl:col-span-6">
            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.sort_by') }}</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select name="sort" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('messages.latest') }}</option>
                    <option value="name-asc" {{ request('sort') == 'name-asc' ? 'selected' : '' }}>{{ __('messages.name_a_to_z') }}</option>
                    <option value="name-desc" {{ request('sort') == 'name-desc' ? 'selected' : '' }}>{{ __('messages.name_z_to_a') }}</option>
                </select>
                <button type="submit" class="px-6 py-3 btn-primary text-white rounded-lg font-semibold transition hover:shadow-lg">
                    {{ __('messages.apply_filters') }}
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Categories Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
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

<!-- Pagination -->
@if($categories->hasPages())
    <div class="flex justify-center">
        {{ $categories->links() }}
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
    
    // Clear filters button
    document.getElementById('clearFilters').addEventListener('click', function() {
        // Get current URL
        const url = new URL(window.location);
        
        // Remove filter parameters
        url.searchParams.delete('search');
        url.searchParams.delete('sort');
        
        // Redirect to the clean URL
        window.location.href = url.toString();
    });
</script>
@endpush
@endsection