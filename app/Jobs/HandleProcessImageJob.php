<?php

namespace App\Jobs;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Services\Media\MediaConversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HandleProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string          $query,
        private int             $count,
        private MediaConversion $conversion,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ImageAPIServiceContract $imageSrv): void
    {
        $result = $imageSrv->search($this->query);
        $cacheKey = Str::random().time();
        Cache::put($cacheKey, $result['images_results'], today()->addDay());

        $job = new ProcessImageJob($this->query, $this->conversion, $cacheKey);
        $batch = array_fill(0, $this->count, $job);

        /**
         * We have different solutions for running image processing concurrently here we will be using laravel built in job batch
         */
        Bus::batch($batch)->dispatch();
    }
}
