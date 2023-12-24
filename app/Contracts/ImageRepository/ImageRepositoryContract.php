<?php

namespace App\Contracts\ImageRepository;

interface ImageRepositoryContract
{
    public function storeMany(array $items);
}
