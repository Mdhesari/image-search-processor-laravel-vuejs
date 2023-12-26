<?php

namespace App\Providers;

use App\Contracts\BatchRepository\BatchRepositoryContract;
use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Repositories\Postgres\BatchRepository;
use App\Repositories\Postgres\ImageRepository;
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
        $this->repositories();

        $this->services();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        dd(Storage::cloud());
    }

    private function repositories()
    {
        $this->app->bind(ImageRepositoryContract::class, function (Application $app) {
            return new ImageRepository(DB::Connection(), 'images');
        });

        $this->app->bind(BatchRepositoryContract::class, function (Application $app) {
            return new BatchRepository(DB::connection(), 'job_batches');
        });
    }

    private function services()
    {
        $this->app->singleton(MediaServiceContract::class, function (Application $app) {
            return new MediaService(Storage::getFacadeRoot());
        });

        $this->app->singleton(ImageAPIServiceContract::class, function (Application $app) {
            return new SerAPIService(config('services.serapi.key'));
        });
    }
}
