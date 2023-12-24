<?php

namespace App\Services\Media;

class MediaConfig
{
    public function __construct(
        public string $Path,
        public int    $MaxSize // KB
    )
    {
        //
    }

    public function path(string $path): string
    {
        return $this->Path = rtrim($this->Path, '/').'/'.ltrim($path, '/');
    }
}
