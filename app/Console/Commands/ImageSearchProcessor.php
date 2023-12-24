<?php

namespace App\Console\Commands;

use App\Http\Resources\ImageResource;
use App\Services\SerAPI\SerAPIService;
use Illuminate\Console\Command;

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
     */
    public function handle()
    {
        $query = $this->argument('query');
        $count = $this->option('count');

        // search for the query
        $searchResult = app(SerAPIService::class)->search($query);

        // fetch the specified count
        new ImageResource();

        // store on db storage

        // result
    }
}
