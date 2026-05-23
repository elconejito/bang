<?php

namespace App\Http\Controllers\API;

use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
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

    public function show(Purchase $purchase): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Purchase $purchase): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
