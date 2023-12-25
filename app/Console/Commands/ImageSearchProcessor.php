<?php

namespace App\Console\Commands;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Jobs\ProcessImageJob;
use App\Services\Media\MediaConversion;
use App\Services\SerAPI\Exceptions\SerAPIException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageSearchProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process {query} {--count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process image search query.';

    /**
     * Execute the console command.
     * @throws \Throwable
     */
    public function handle()
    {
        $query = $this->argument('query');
        $count = $this->option('count');

        try {
            $items = Cache::rememberForever('test', function () use ($query, $count) {
                $searchResult = app(ImageApiServiceContract::class)->search($query);

                return array_slice($searchResult['images_results'], 0, $count);
            });

            $conversion = new MediaConversion(
                config('services.media.conversion_width'),
                config('services.media.conversion_height')
            );

            /**
             * We have different solutions for running image processing concurrently here we will be using laravel built in job batch
             */
            $items = array_map(fn($i) => new ProcessImageJob($i, $conversion), $items);
            $batch = Bus::batch($items)->dispatch();

            $this->info('batching '.$batch->id);

            $this->info('Images are store in db successfully...');
        } catch (SerAPIException $e) {
            Log::critical('SerAPI service error: '.$e->getMessage());

            $this->error($e->getMessage());
        } catch (\Exception $e) {
            Log::critical($e->getMessage());

            throw $e;
        }

    }
}
