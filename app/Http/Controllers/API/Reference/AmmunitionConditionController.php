<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\AmmunitionCondition;
use App\Transformers\AmmunitionConditionTransformer;
use Illuminate\Http\JsonResponse;

class AmmunitionConditionController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(AmmunitionCondition::orderBy('label')->get(), AmmunitionConditionTransformer::class)->respond();
    }
}
