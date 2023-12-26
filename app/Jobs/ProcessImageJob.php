<?php

namespace App\Jobs;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Services\Media\MediaConfig;
use App\Services\Media\MediaConversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        private array           $items,
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
        try {
            // Handle new images for new try attempts
            $media = $this->items[$this->attempts() - 1] ?? $this->items[count($this->items) - 1];

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
        } catch (\Exception $e) {
            if ($this->attempts() > $this->maxExceptions) {
                throw $e;
            }

            $this->release(180);

            return;
        }
    }

    public function retryUntil()
    {
        // will keep retrying, by backoff logic below
        // until 12 hours from first run.
        // After that, if it fails it will go
        // to the failed_jobs table
        return now()->addHours(12);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        // first 5 retries, after first failure
        // will be 5 minutes (300 seconds) apart,
        // further attempts will be
        // 3 hours (10,800 seconds) after
        // previous attempt
        return [300, 300, 300, 300, 300, 10800];
    }
}
