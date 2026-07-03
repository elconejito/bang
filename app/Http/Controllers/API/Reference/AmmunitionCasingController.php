<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\AmmunitionCasing;
use App\Transformers\AmmunitionCasingTransformer;
use Illuminate\Http\JsonResponse;

class AmmunitionCasingController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(AmmunitionCasing::orderBy('label')->get(), AmmunitionCasingTransformer::class)->respond();
    }
}
