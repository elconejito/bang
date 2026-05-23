<?php

namespace App\Http\Controllers\API;

use App\Models\Target;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index(): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Target $target): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Target $target): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Target $target): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
