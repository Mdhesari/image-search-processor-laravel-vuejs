<?php

namespace App\Http\Controllers;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Contracts\ImageRepository\ImageRepositoryContract;
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
    /**
     * @param ImageRepositoryContract $repo
     * @return ImageResource
     */
    public function index(ImageRepositoryContract $repo): ImageResource
    {
        return new ImageResource($repo->getAll());
    }

    /**
     * @param SearchProcessRequest $req
     * @param ImageAPIServiceContract $imgSrv
     * @return ResponseResource
     * @throws \Throwable
     */
    public function searchProcess(SearchProcessRequest $req, ImageAPIServiceContract $imgSrv)
    {
        $data = $req->validated();

        try {
            $items = Cache::remember('image:'.$data['query'].$data['count'], now()->addMinutes(5), function () use ($data, $imgSrv) {
                $searchResult = $imgSrv->search($data['query']);

                return array_slice($searchResult['images_results'], 0, $data['count']);
            });

            $conversion = new MediaConversion(
                $data['width'] ?? config('services.media.conversion_width'),
                $data['height'] ?? config('services.media.conversion_height')
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
