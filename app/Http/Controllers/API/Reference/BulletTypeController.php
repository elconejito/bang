<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\BulletType;
use App\Transformers\BulletTypeTransformer;
use Illuminate\Http\JsonResponse;

class BulletTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(BulletType::orderBy('label')->get(), BulletTypeTransformer::class)->respond();
    }
}
