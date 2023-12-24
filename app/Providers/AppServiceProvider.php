<?php

namespace App\Providers;

use App\Adapter\SerAPI\SerAPIAdapter;
use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Repositories\Postgres\ImageRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
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

        $this->app->bind(ImageRepositoryContract::class, function (Application $app) {
            return new ImageRepository(DB::Connection(), 'images');
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
