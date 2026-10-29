<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\RouteRepository;
use App\Services\RouteService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RouteRepository::class, function ($app) {
            return new RouteRepository();
        });

        $this->app->bind(RouteService::class, function ($app) {
            return new RouteService($app->make(RouteRepository::class));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 