<?php

namespace App\Repositories\Postgres;

use App\Contracts\ImageRepository\ImageRepositoryContract;
use Illuminate\Database\ConnectionInterface;

/**
 * We avoid using eloquent in this repo for better performance
 */
class ImageRepository implements ImageRepositoryContract
{
    const DEFAULT_PER_PAGE = 10;

    public function __construct(
        private ConnectionInterface $connection,
        private string              $table
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
            'query'           => $item['query'],
            'image'           => $item['image'],
            'original'        => $item['original'],
            'original_width'  => $item['original_width'],
            'original_height' => $item['original_height'],
            'resized_width'   => $item['resized_width'],
            'resized_height'  => $item['resized_height'],
        ], $items);

        $res = $this->builder()->insert($items);
        if (! $res) {

            throw new \Exception('Could not insert items.');
        }
    }

    private function builder(): \Illuminate\Database\Query\Builder
    {
        return $this->connection->table($this->table);
    }

    /**
     * @param array $item
     * @throws \Exception
     */
    public function store(array $item)
    {
        $item = [
            'title'           => $item['title'],
            'query'           => $item['query'] ?? '',//TODO: should retrieve query
            'image'           => $item['image'],
            'original'        => $item['original'],
            'original_width'  => $item['original_width'],
            'original_height' => $item['original_height'],
            'resized_width'   => $item['resized_width'],
            'resized_height'  => $item['resized_height'],
            'updated_at'      => now(),
            'created_at'      => now(),
        ];

        $res = $this->builder()->insert($item);
        if (! $res) {

            throw new \Exception('Could not insert item.');
        }
    }

    public function getAll(?array $params = null)
    {
        $params['per_page'] = $params['per_page'] ?? self::DEFAULT_PER_PAGE;

        return $this->builder()->latest()->paginate($params['per_page']);
    }
}
