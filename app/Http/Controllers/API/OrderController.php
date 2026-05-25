<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        // TODO: implement
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
