<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\API\TemuService;
use App\Services\API\CJService;
use App\Services\API\SheinService;

class ApiServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TemuService::class, function ($app) {
            return new TemuService();
        });
        
        $this->app->singleton(CJService::class, function ($app) {
            return new CJService();
        });
        
        $this->app->singleton(SheinService::class, function ($app) {
            return new SheinService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}