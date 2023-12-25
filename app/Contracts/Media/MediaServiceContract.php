<?php

namespace App\Contracts\Media;

use App\Services\Media\MediaConversion;
use Illuminate\Http\File;

interface MediaServiceContract
{
    public function mediaFromUrl(string $url);

    public function media(File $file);

    public function conversion(MediaConversion $conversion);

    public function getUrl();

    public function apply();
}
