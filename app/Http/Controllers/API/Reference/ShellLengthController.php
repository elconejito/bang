<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\ShellLength;
use App\Transformers\ShellLengthTransformer;
use Illuminate\Http\JsonResponse;

class ShellLengthController extends Controller
{
    public function index(): JsonResponse
    {
        return fractal(ShellLength::orderBy('label')->get(), ShellLengthTransformer::class)->respond();
    }
}
