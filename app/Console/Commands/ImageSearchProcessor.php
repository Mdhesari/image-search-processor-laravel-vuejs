<?php

namespace App\Console\Commands;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Services\Media\MediaConversion;
use App\Services\SerAPI\Exceptions\SerAPIException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageSearchProcessor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process {query} {--count=1}';

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
            $items = Cache::rememberForever('test', function () use ($query, $count) {
                $searchResult = app(ImageApiServiceContract::class)->search($query);

                return array_slice($searchResult['images_results'], 0, $count);
            });

            $conversion = [
                'width'  => config('services.media.conversion_width'),
                'height' => config('services.media.conversion_height'),
            ];
            foreach ($items as $key => $item) {
                $items[$key]['image'] = app(MediaServiceContract::class)->mediaUrl($item['original'])->conversion(
                    new MediaConversion($conversion['width'], $conversion['height'])
                )->apply()->save()->getUrl();
            }

            app(ImageRepositoryContract::class)->storeMany($items);

            $this->info('Images are store in db successfully...');
        } catch (SerAPIException $e) {
            Log::critical('SerAPI service error: '.$e->getMessage());

            $this->error($e->getMessage());
        } catch (\Exception $e) {
            Log::critical($e->getMessage());

            $this->error($e->getMessage());
        }

    }
}
