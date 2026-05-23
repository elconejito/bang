<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\CaliberType;
use App\Transformers\CaliberTypeTransformer;
use Illuminate\Http\JsonResponse;

class CaliberTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(CaliberType::all(), CaliberTypeTransformer::class)->respond();
    }
}
