<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Purchase::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Purchase::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        $this->authorize('view', $purchase);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Purchase $purchase): JsonResponse
    {
        $this->authorize('update', $purchase);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $this->authorize('delete', $purchase);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
