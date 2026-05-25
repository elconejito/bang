<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Range;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RangeController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Range::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Range::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Range $range): JsonResponse
    {
        $this->authorize('view', $range);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Range $range): JsonResponse
    {
        $this->authorize('update', $range);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Range $range): JsonResponse
    {
        $this->authorize('delete', $range);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
