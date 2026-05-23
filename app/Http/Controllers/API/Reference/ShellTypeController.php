<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\ShellType;
use App\Transformers\ShellTypeTransformer;
use Illuminate\Http\JsonResponse;

class ShellTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(ShellType::all(), ShellTypeTransformer::class)->respond();
    }
}
