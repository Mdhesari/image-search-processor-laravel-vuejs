<?php

namespace App\Providers;

use App\Services\SerAPI\SerAPIService;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SerAPIService::class, function (Application $app) {
            return new SerAPIService(config('services.serapi.apiKey'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
