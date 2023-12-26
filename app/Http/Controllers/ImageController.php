<?php

namespace App\Http\Controllers;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use App\Http\Requests\SearchProcessRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ResponseResource;
use App\Jobs\HandleProcessImageJob;
use App\Services\Media\MediaConversion;

class ImageController extends Controller
{
    /**
     * @param ImageRepositoryContract $repo
     * @return ImageResource
     */
    public function index(ImageRepositoryContract $repo): ImageResource
    {
        return new ImageResource($repo->get());
    }

    /**
     * @param SearchProcessRequest $req
     * @return ResponseResource
     * @throws \Throwable
     */
    public function searchProcess(SearchProcessRequest $req): ResponseResource
    {
        $data = $req->validated();

        $conversion = new MediaConversion(
            $data['width'] ?? config('services.media.conversion_width'),
            $data['height'] ?? config('services.media.conversion_height')
        );

        dispatch(new HandleProcessImageJob($data['query'], $data['count'], $conversion));

        return new ResponseResource([]);
    }
}
