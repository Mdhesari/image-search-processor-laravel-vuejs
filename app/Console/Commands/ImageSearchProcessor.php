<?php

namespace App\Console\Commands;

use App\Jobs\HandleProcessImageJob;
use App\Services\Media\MediaConversion;
use Illuminate\Console\Command;

class ImageSearchProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process {query} {--width=} {--height=} {--count=10}';

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
        $width = $this->option('width') ?: config('services.media.conversion_width');
        $height = $this->option('width') ?: config('services.media.conversion_height');

        dispatch(new HandleProcessImageJob($query, $count, new MediaConversion($width, $height)));

        $this->info('Your request has been processed and sent to queue.');

        return self::SUCCESS;
    }
}
