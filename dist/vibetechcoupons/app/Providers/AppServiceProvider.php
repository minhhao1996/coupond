<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Livewire 4 uses an APP_KEY-derived, hashed update endpoint.
        // Register it explicitly so Laravel 13 always boots the current route
        // after APP_KEY/config changes instead of relying on stale cached routes.
        Livewire::setUpdateRoute(function ($handle, $path) {
            return Route::post($path, $handle)->middleware('web');
        });
    }
}
