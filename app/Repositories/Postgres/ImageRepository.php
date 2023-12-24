<?php

namespace App\Repositories\Postgres;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use Illuminate\Database\PostgresConnection;

class ImageRepository implements ImageRepositoryContract
{
    public function __construct(
        private PostgresConnection $connection,
        private string             $table
    )
    {
        //
    }

    /**
     * @param array $items
     * @throws \Exception
     */
    public function storeMany(array $items)
    {
        // filter
        $items = array_map(fn($item) => [
            'title'           => $item['title'],
            'query'           => $item['query'] ?? '',//TODO: should retrieve query
            'image'           => $item['original'],
            'original'        => $item['original'],
            'original_width'  => $item['original_width'],
            'original_height' => $item['original_height'],
            'resized_width'   => 0, //TODO: consider resizing
            'resized_height'  => 0, //TODO: consider resizing
        ], $items);

        $result = $this->builder()->insert($items);
        if (! $result) {

            throw new \Exception('Could not insert items.');
        }
    }

    private function builder(): \Illuminate\Database\Query\Builder
    {
        return $this->connection->table($this->table);
    }
}
