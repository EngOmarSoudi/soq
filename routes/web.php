<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AliExpressController;
use App\Http\Controllers\StaticPageController;

// Authentication Routes
Auth::routes();

// Public Routes
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/categories', [ProductController::class, 'categories'])->name('categories.index');

// Static Pages
Route::get('/pages/{slug}', [StaticPageController::class, 'show'])->name('static-pages.show');

// Locale & Theme Routes
Route::get('/{locale}', [LocaleController::class, 'setLocale'])
    ->where('locale', 'ar|en')
    ->name('set-locale');
Route::post('/set-theme', [ThemeController::class, 'setTheme'])->name('set-theme');

// Cart Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.apply_coupon');
    Route::post('/cart/calculate-shipping', [CartController::class, 'calculateShippingForAddress'])->name('cart.calculate_shipping');
    Route::post('/order/place', [CartController::class, 'placeOrder'])->name('order.place');
    Route::get('/order/confirmation/{id}', [CartController::class, 'confirmation'])->name('order.confirmation');
    
    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{id}/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/api/wishlist/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    
    // Review Routes
    Route::post('/reviews', [ReviewController::class, 'add'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profile/address/add', [ProfileController::class, 'addAddress'])->name('profile.address.add');
    Route::post('/profile/address/{id}/delete', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    
    // AliExpress Routes
    Route::post('/aliexpress/import', [AliExpressController::class, 'importProduct'])->name('aliexpress.import');
    Route::post('/aliexpress/shipping-estimate', [AliExpressController::class, 'estimateShipping'])->name('aliexpress.shipping_estimate');
});

// Admin Routes (Protected with auth and admin check)
Route::middleware(['auth', 'admin'])->group(function () {
    // Filament admin panel is auto-registered but we add additional protection
    Route::get('/admin/dashboard', function () {
        return redirect('/admin');
    })->name('admin.dashboard');
});