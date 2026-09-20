<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\ShotWeight;
use App\Transformers\ShotWeightTransformer;
use Illuminate\Http\JsonResponse;

class ShotWeightController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(ShotWeight::query()->orderBy('id')->get(), ShotWeightTransformer::class)->respond();
    }
}
