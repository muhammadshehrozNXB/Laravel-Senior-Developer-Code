<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (env('CDN_URL')) {
            URL::macro('asset', function ($path) {
                return env('CDN_URL') . '/' . ltrim($path, '/');
            });
        }
    }
}
