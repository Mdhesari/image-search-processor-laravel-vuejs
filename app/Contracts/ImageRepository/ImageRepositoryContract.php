<?php

namespace App\Contracts\ImageRepository;

interface ImageRepositoryContract
{
    public function storeMany(array $items);

    public function store(array $item);

    public function getAll(?array $params = null);
}
