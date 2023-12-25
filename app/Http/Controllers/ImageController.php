<?php

namespace App\Http\Controllers;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Contracts\Media\MediaServiceContract;
use App\Http\Requests\SearchProcessRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ResponseResource;
use App\Jobs\ProcessImageJob;
use App\Services\Media\MediaConversion;
use App\Services\SerAPI\Exceptions\SerAPIException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    public function index(ImageRepositoryContract $repo): ImageResource
    {
        return new ImageResource($repo->getAll());
    }

    public function searchProcess(SearchProcessRequest $req, ImageAPIServiceContract $imgSrv, MediaServiceContract $mediaSrv)
    {
        $response = new ResponseResource([]);

        $query = $req->query;
        $count = $req->count;

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

            $response = new ResponseResource([
                'batch' => $batch->id,
            ]);
        } catch (SerAPIException $e) {
            Log::critical('SerAPI service error: '.$e->getMessage());

            $response = new ResponseResource([
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            Log::critical($e->getMessage());

            $response = new ResponseResource([
                'error' => 'Unexpected error.',
            ]);
        }

        return $response;
    }
}
