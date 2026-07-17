<?php

namespace App\Http\Controllers\API;

use App\Actions\Orders\SyncOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Ammunition;
use App\Models\Order;
use App\Transformers\OrderTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::with(['store', 'inventories.ammunition.caliber'])
            ->latest('order_date')
            ->get();

        return fractal($orders, OrderTransformer::class)->respond();
    }

    public function store(StoreOrderRequest $request, SyncOrder $syncOrder): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $syncOrder->execute(null, $request->validated(), Auth::id());

        return fractal($order, OrderTransformer::class)->respond(201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return fractal($order, OrderTransformer::class)->respond();
    }

    public function update(UpdateOrderRequest $request, Order $order, SyncOrder $syncOrder): JsonResponse
    {
        $this->authorize('update', $order);

        $order = $syncOrder->execute($order, $request->validated(), Auth::id());

        return fractal($order, OrderTransformer::class)->respond();
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        DB::transaction(function () use ($order): void {
            $ammunitionIds = $order->inventories()->pluck('ammunition_id');
            $order->inventories()->delete();
            $order->delete();

            Ammunition::whereIn('id', $ammunitionIds)
                ->get()
                ->each(fn (Ammunition $ammunition) => $ammunition->recalculateInventory());
        });

        return response()->json(null, 204);
    }
}
