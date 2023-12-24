<?php

namespace App\Contracts\Media;

use Illuminate\Http\File;

interface MediaServiceContract
{
    public function mediaUrl(string $url);

    public function media(File $file);

    public function conversion(array $conversion);

    public function getUrl();
}
