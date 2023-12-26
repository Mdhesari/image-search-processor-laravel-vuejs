<?php

namespace App\Contracts\BatchRepository;

interface BatchRepositoryContract
{
    public function get(?array $params = null);
}
