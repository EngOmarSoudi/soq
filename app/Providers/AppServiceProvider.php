<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale from session or user preferences
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $preferences = auth()->user()->preferences ?? [];
                $locale = $preferences['locale'] ?? session('locale', config('app.locale'));
                $theme = $preferences['theme'] ?? session('theme', 'light');
            } else {
                $locale = session('locale', config('app.locale'));
                $theme = session('theme', 'light');
            }
            
            app()->setLocale($locale);
            session(['locale' => $locale, 'theme' => $theme]);
        });
        
        // Share categories with all views
        view()->composer('*', function ($view) {
            $categories = Category::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            $view->with('sharedCategories', $categories);
        });

        // Share site settings with all views
        view()->composer('*', function ($view) {
            $siteSetting = SiteSetting::first() ?? new SiteSetting([
                'site_name' => 'EcommStore',
                'site_email' => 'info@example.com',
                'site_address' => '123 E-commerce St, Digital City',
                'site_description' => 'Your trusted online marketplace.',
            ]);
            $view->with('siteSetting', $siteSetting);
        });
    }
}
