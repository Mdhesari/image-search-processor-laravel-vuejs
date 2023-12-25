<?php

namespace App\Http\Controllers;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Http\Requests\SearchProcessRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ResponseResource;
use App\Jobs\HandleProcessImageJob;
use App\Services\Media\MediaConversion;
use App\Services\SerAPI\Exceptions\SerAPIException;
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
     * @return ResponseResource
     * @throws \Throwable
     */
    public function searchProcess(SearchProcessRequest $req): ResponseResource
    {
        $data = $req->validated();

        try {
            $conversion = new MediaConversion(
                $data['width'] ?? config('services.media.conversion_width'),
                $data['height'] ?? config('services.media.conversion_height')
            );

            dispatch(new HandleProcessImageJob($data['query'], $data['count'], $conversion));

            $response = new ResponseResource([]);
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
