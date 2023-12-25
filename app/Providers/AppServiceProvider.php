<?php

namespace App\Providers;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Repositories\Postgres\ImageRepository;
use App\Services\Media\MediaConfig;
use App\Services\Media\MediaService;
use App\Services\SerAPI\SerAPIService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageAPIServiceContract::class, function (Application $app) {
            return new SerAPIService(config('services.serapi.key'));
        });

        $this->app->bind(ImageRepositoryContract::class, function (Application $app) {
            return new ImageRepository(DB::Connection(), 'images');
        });

        $this->app->singleton(MediaServiceContract::class, function (Application $app) {
            return new MediaService(Storage::getFacadeRoot());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        dd(Storage::cloud());
    }
}
