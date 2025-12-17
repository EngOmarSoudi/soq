<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);
        
        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        // Category Filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Price Range Filter
        if ($request->has('price_range') && $request->price_range) {
            switch($request->price_range) {
                case '0-50':
                    $query->whereBetween('price', [0, 50]);
                    break;
                case '50-100':
                    $query->whereBetween('price', [50, 100]);
                    break;
                case '100-500':
                    $query->whereBetween('price', [100, 500]);
                    break;
                case '500+':
                    $query->where('price', '>=', 500);
                    break;
            }
        }
        
        // Color Filter
        if ($request->has('color') && $request->color) {
            $query->where(function($q) use ($request) {
                $q->whereRaw("JSON_CONTAINS(colors, JSON_QUOTE(?), '$.en')", [$request->color])
                  ->orWhereRaw("JSON_CONTAINS(colors, JSON_QUOTE(?), '$.ar')", [$request->color]);
            });
        }
        
        // Size Filter
        if ($request->has('size') && $request->size) {
            $query->where(function($q) use ($request) {
                $q->whereRaw("JSON_CONTAINS(sizes, JSON_QUOTE(?), '$.en')", [$request->size])
                  ->orWhereRaw("JSON_CONTAINS(sizes, JSON_QUOTE(?), '$.ar')", [$request->size]);
            });
        }
        
        // Sorting
        switch($request->get('sort', 'latest')) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();
        
        // Get all available colors and sizes for filtering
        $allProducts = Product::where('is_active', true)->get();
        $availableColors = [];
        $availableSizes = [];
        
        foreach ($allProducts as $product) {
            if ($product->colors) {
                foreach ($product->colors as $lang => $langColors) {
                    if (is_array($langColors)) {
                        $availableColors = array_merge($availableColors, $langColors);
                    }
                }
            }
            if ($product->sizes) {
                foreach ($product->sizes as $lang => $langSizes) {
                    if (is_array($langSizes)) {
                        $availableSizes = array_merge($availableSizes, $langSizes);
                    }
                }
            }
        }
        
        $availableColors = array_unique($availableColors);
        $availableSizes = array_unique($availableSizes);
        
        return view('products.index', compact('products', 'categories', 'availableColors', 'availableSizes'));
    }
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        // Increment view count
        $product->increment('views_count');
        
        // Check if product is in user's cart and get quantity
        $isInCart = false;
        $cartQuantity = 0;
        if (auth()->check()) {
            $cartItem = auth()->user()->cartItems()->where('product_id', $product->id)->first();
            if ($cartItem) {
                $isInCart = true;
                $cartQuantity = $cartItem->quantity;
            }
        }
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();
        
        $reviews = $product->reviews()->where('status', 'approved')->with('user')->latest()->paginate(5);
        
        // Check if current user has already reviewed this product
        $userReview = null;
        if (auth()->check()) {
            $userReview = $product->reviews()->where('user_id', auth()->id())->first();
        }
        
        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'isInCart', 'cartQuantity', 'userReview'));
    }
    
    public function categories(Request $request)
    {
        $query = Category::where('is_active', true);
        
        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ["%{$searchTerm}%"]);
            });
        }
        
        // Sorting
        switch($request->get('sort', 'latest')) {
            case 'name-asc':
                $query->orderByRaw("JSON_EXTRACT(name, '$.en') ASC");
                break;
            case 'name-desc':
                $query->orderByRaw("JSON_EXTRACT(name, '$.en') DESC");
                break;
            default:
                $query->latest();
        }
        
        $categories = $query->paginate(12);
        
        return view('categories.index', compact('categories'));
    }
    
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products = $category->products()->where('is_active', true)->paginate(12);
        $categories = Category::where('is_active', true)->get();
        
        // Get all available colors and sizes for filtering
        $allProducts = Product::where('is_active', true)->get();
        $availableColors = [];
        $availableSizes = [];
        
        foreach ($allProducts as $product) {
            if ($product->colors) {
                foreach ($product->colors as $lang => $langColors) {
                    if (is_array($langColors)) {
                        $availableColors = array_merge($availableColors, $langColors);
                    }
                }
            }
            if ($product->sizes) {
                foreach ($product->sizes as $lang => $langSizes) {
                    if (is_array($langSizes)) {
                        $availableSizes = array_merge($availableSizes, $langSizes);
                    }
                }
            }
        }
        
        $availableColors = array_unique($availableColors);
        $availableSizes = array_unique($availableSizes);
        
        return view('products.index', compact('products', 'categories', 'category', 'availableColors', 'availableSizes'));
    }
}
