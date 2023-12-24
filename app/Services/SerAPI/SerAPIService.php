<?php

namespace App\Services\SerAPI;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use App\Services\SerAPI\Exceptions\SerAPIException;
use Illuminate\Support\Facades\Http;

class SerAPIService implements ImageAPIServiceContract
{
    public function __construct(
        private string $apiKey
    )
    {
        //
    }

    /**
     * @param string $query
     * @return array
     * @throws SerAPIException
     */
    public function search(string $query): array
    {
        $response = Http::get("https://serpapi.com/search.json?engine=google_images&api_key={$this->apiKey}&q={$query}&hl=en&gl=us&tbs=il:cl");
        if (isset($response['error'])) {

            throw new SerAPIException($response['error'], 500);
        }

        return $response->json();
    }
}
