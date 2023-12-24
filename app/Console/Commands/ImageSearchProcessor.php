<?php

namespace App\Console\Commands;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Services\SerAPI\SerAPIService;
use Illuminate\Console\Command;
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
     */
    public function handle()
    {
        $query = $this->argument('query');
        $count = $this->option('count');

        try {
            $searchResult = app(SerAPIService::class)->search($query);
        } catch (\Exception $e) {
            Log::critical('SerAPI service error: '.$e->getMessage());

            $this->error($e->getMessage());

            return;
        }

        $items = array_slice($searchResult['images_results'], 0, $count);

        try {
            app(ImageRepositoryContract::class)->storeMany($items);

            $this->info('Images are store in db successfully...');
        } catch (\Exception $e) {
            Log::critical('Image repository error: '.$e->getMessage());

            $this->error($e->getMessage());
        }
    }
}
