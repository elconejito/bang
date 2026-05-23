<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\LocationType;
use App\Transformers\LocationTypeTransformer;
use Illuminate\Http\JsonResponse;

class LocationTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(LocationType::all(), LocationTypeTransformer::class)->respond();
    }
}
