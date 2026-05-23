<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\Purpose;
use App\Transformers\PurposeTransformer;
use Illuminate\Http\JsonResponse;

class PurposeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(Purpose::all(), PurposeTransformer::class)->respond();
    }
}
