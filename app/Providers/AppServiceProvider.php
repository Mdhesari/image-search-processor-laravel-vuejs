<?php

namespace App\Providers;

use App\Adapter\SerAPI\SerAPIAdapter;
use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageAPIServiceContract::class, function (Application $app) {
            return new SerAPIAdapter(config('services.serapi.key'));
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
