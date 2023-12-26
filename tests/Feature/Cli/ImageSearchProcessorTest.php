<?php

namespace Tests\Feature\Cli;

use App\Contracts\ImageAPIService\ImageAPIServiceContract;
use Mockery\MockInterface;
use Tests\TestCase;

class ImageSearchProcessorTest extends TestCase
{
    public function test_can_image_search_processor()
    {
        $this->mock(ImageAPIServiceContract::class, function (MockInterface $mock) {
            $items = json_decode(file_get_contents(__DIR__.'/../../data/serapi_mock_data.json'), true);

            $mock->shouldReceive('search')->once()->andReturn([
                'images_results' => $items,
            ]);
        });

        $this->artisan('app:process "cute kittens" --width=1500 --height=1500 --count=3')->assertSuccessful();
    }
}
