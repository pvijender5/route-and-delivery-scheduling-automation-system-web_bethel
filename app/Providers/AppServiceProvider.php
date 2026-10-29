<?php

namespace App\Providers;
use App\Services\AcumeticaService;
use App\Services\AuthService;

use Illuminate\Support\ServiceProvider;
use App\Services\CurlService;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('CurlService', function ($app) {
            return new CurlService(); // Make sure the service class is resolved properly
        });
        // Register ShipmentService
        $this->app->singleton('AcumeticaService', function ($app) {
            return new \App\Services\AcumeticaService();
        });
        $this->app->singleton('AuthService', function ($app) {
            return new \App\Services\AuthService();
        });
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
