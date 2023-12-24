<?php

namespace App\Adapter\Media;

use App\Contracts\Media\MediaServiceContract;
use App\Services\Media\MediaConversion;
use App\Services\Media\MediaService;
use Illuminate\Http\File;

// Separation of concerns
class MediaAdapter implements MediaServiceContract
{
    public function __construct(
        private MediaService $service
    )
    {
        //
    }

    public function mediaUrl(string $url)
    {
        return $this->service->mediaUrl($url);
    }

    public function media(File $file)
    {
        return $this->service->media($file);
    }

    public function conversion(array $conversion)
    {
        return $this->service->conversion(
            new MediaConversion($conversion['width'], $conversion['height'])
        );
    }

    public function getUrl()
    {
        return $this->service->getUrl();
    }
}
