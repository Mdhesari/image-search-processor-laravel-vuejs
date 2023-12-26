<?php

namespace App\Jobs;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Services\Media\MediaConversion;
use App\Services\SerAPI\Exceptions\SerAPIException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * @throws SerAPIException
     */
    public function handle(ImageAPIServiceContract $imageSrv): void
    {
        $result = $imageSrv->search($this->query)['images_results'];

        // in order to have replaceable media
        $result = array_chunk($result, 5);

        if (count($result) < $this->count) {
            // TODO: this is temporary for handling big count
            $result = array_fill(count($result), $this->count - count($result), $result[0]);
        }

        for ($i = 0; $i < $this->count; $i++) {
            dispatch(new ProcessImageJob($this->query, $this->conversion, $result[$i]));
        }
    }
}
