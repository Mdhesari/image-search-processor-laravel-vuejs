<?php

namespace App\Repositories\Postgres;

use App\Contracts\BatchRepository\BatchRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

class BatchRepository implements BatchRepositoryContract
{
    public function __construct(
        private ConnectionInterface $connection,
        private string              $table,
        private int                 $perPage = 10,
    )
    {
        //
    }

    /**
     * @param array|null $params
     * @return LengthAwarePaginator
     */
    public function get(?array $params = null)
    {
        $query = $this->builder();

        if (isset($params['name'])) {
            $query->where('name', $params['name']);
        }

        if (isset($params['status']) && $params['status'] == 'pending') {
            $query->whereNull('finished_at');
        }

        return $query->paginate($params['per_page'] ?? $this->perPage);
    }

    private function builder(): Builder
    {
        return $this->connection->table($this->table);
    }
}
