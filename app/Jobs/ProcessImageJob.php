<?php

namespace App\Jobs;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Services\Media\MediaConversion;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;

class ProcessImageJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private array           $item,
        private MediaConversion $conversion,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MediaServiceContract $srv, ImageRepositoryContract $repo): void
    {
        $url = $srv->mediaFromUrl($this->item['original']);

        if (! $url) {

            return;
        }
        $url = $url->conversion($this->conversion)->apply()->save()->getUrl();

        $this->item['image'] = $url;
        $this->item['resized_width'] = $this->conversion->Width;
        $this->item['resized_height'] = $this->conversion->Height;

        $repo->store($this->item);
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        // It does not matter if other image processor are failed
        return [new SkipIfBatchCancelled];
    }
}
