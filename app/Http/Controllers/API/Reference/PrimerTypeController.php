<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\PrimerType;
use App\Transformers\PrimerTypeTransformer;
use Illuminate\Http\JsonResponse;

class PrimerTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(PrimerType::orderBy('label')->get(), PrimerTypeTransformer::class)->respond();
    }
}
