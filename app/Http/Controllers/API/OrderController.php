<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
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

    public function show(Order $order): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Order $order): JsonResponse
    {
        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
