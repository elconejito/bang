<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\ShotMaterial;
use App\Transformers\ShotMaterialTransformer;
use Illuminate\Http\JsonResponse;

class ShotMaterialController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(ShotMaterial::orderBy('label')->get(), ShotMaterialTransformer::class)->respond();
    }
}
