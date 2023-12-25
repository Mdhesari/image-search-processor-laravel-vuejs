<?php

namespace App\Jobs;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Services\Media\MediaConfig;
use App\Services\Media\MediaConversion;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ProcessImageJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 25;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string          $query,
        private MediaConversion $conversion,
        private string          $cacheKey,
    )
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(ImageRepositoryContract $repo, MediaServiceContract $mediaSrv): void
    {
        /**
         * Sometimes the fetched images are not correct, so we do our best to do our job :)
         */
        Redis::throttle($this->batchId)->allow(10)->every(60)->then(function () use ($repo, $mediaSrv) {
            $media = $this->getMediaItem();

            $url = $mediaSrv->config(new MediaConfig('media', 100000))
                ->mediaFromUrl($media['original'])
                ->conversion($this->conversion)
                ->apply()
                ->save()
                ->getUrl();

            $media['query'] = $this->query;
            $media['image'] = $url;
            $media['resized_width'] = $this->conversion->Width;
            $media['resized_height'] = $this->conversion->Height;

            $repo->store($media);
        }, function () {
            // Unable to obtain lock...
            $this->release(10);
        });
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getMediaItem(): array
    {
        $items = $this->getMediaItems();

        if (! $items || empty($items)) {

            throw new \Exception("Could not fetch media items.");
        }

        $item = $items[0];

        // avoid duplication for other batch jobs
        array_shift($items);
        Cache::put($this->cacheKey, $items);

        return $item;
    }

    private function getMediaItems()
    {
        $query = $this->query;

        // In case of missing the cache key
        return Cache::remember($this->cacheKey, today()->addDay(), function () use ($query) {
            $searchResult = app(ImageAPIServiceContract::class)->search($query);

            return $searchResult['images_results'];
        });
    }
}
