<?php

namespace App\Services\Media;

class MediaConversion
{
    public function __construct(
        public int $Width,
        public int $Height
    )
    {
        //
    }
}
