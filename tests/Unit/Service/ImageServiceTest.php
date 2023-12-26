<?php

namespace Tests\Unit\Service;

use App\Services\SerAPI\SerAPIService;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    public function test_can_search_query(): void
    {
        $service = new SerAPIService(config('services.serapi.key'));

        $response = $service->search('cute kittens');

        $this->assertArrayHasKey('images_results', $response);
        $this->assertNotEmpty($response['images_results']);
    }
}
