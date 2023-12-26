<?php

namespace App\Http\Controllers;

use App\Contracts\BatchRepository\BatchRepositoryContract;
use App\Http\Resources\ResponseResource;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, BatchRepositoryContract $repo): ResponseResource
    {
        return new ResponseResource(
            $repo->get($request->query())
        );
    }
}
