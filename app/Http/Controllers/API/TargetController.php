<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Target;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Target::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Target::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Target $target): JsonResponse
    {
        $this->authorize('view', $target);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Target $target): JsonResponse
    {
        $this->authorize('update', $target);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Target $target): JsonResponse
    {
        $this->authorize('delete', $target);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
