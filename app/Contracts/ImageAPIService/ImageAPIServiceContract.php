<?php

namespace App\Contracts\ImageAPIService;

use App\Services\SerAPI\Exceptions\SerAPIException;

interface ImageAPIServiceContract
{
    /**
     * Search query and retrieve images
     *
     * @param string $query
     * @return array
     * @throws SerAPIException
     */
    public function search(string $query): array;
}
