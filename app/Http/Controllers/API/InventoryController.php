<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Ammunition;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\SessionLine;
use App\Scopes\UserScope;
use App\Transformers\InventoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InventoryController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Inventory::class);

        $inventories = QueryBuilder::for(Inventory::class)
            ->allowedFilters(AllowedFilter::exact('ammunition_id'), 'inventory_date')
            ->allowedSorts('inventory_date', 'rounds')
            ->defaultSort('-inventory_date', 'rounds')
            ->get();

        $sessionLineIds = $inventories->whereNotNull('session_line_id')->pluck('session_line_id');

        if ($sessionLineIds->isNotEmpty()) {
            $sessionLines = SessionLine::withoutGlobalScope(UserScope::class)
                ->with(['trainingSession' => fn ($q) => $q->withoutGlobalScope(UserScope::class)])
                ->whereIn('id', $sessionLineIds)
                ->get()
                ->keyBy('id');

            $inventories->each(function (Inventory $inventory) use ($sessionLines) {
                if ($inventory->session_line_id) {
                    $inventory->setRelation('sessionLine', $sessionLines->get($inventory->session_line_id));
                }
            });
        }

        return fractal($inventories, InventoryTransformer::class)->respond();
    }

    public function store(StoreInventoryRequest $request): JsonResponse
    {
        $this->authorize('create', Inventory::class);

        $extra = ['user_id' => Auth::id()];

        Log::debug(__METHOD__.':'.__LINE__, [$request->all()]);
        if ($request->get('is_purchase')) {
            $order = Order::create([
                ...$request->only(['rounds', 'store_id']),
                'order_date' => $request->get('inventory_date'),
                'total_cost' => $request->get('cost'),
                ...$extra,
            ]);
            $extra['order_id'] = $order->id;
            $extra['cost'] = $request->get('cost');
        }

        $inventory = Inventory::create([
            ...$request->only(['inventory_date', 'ammunition_id', 'rounds']),
            ...$extra,
        ]);

        Ammunition::findOrFail($request->get('ammunition_id'))->recalculateInventory();

        return fractal($inventory, InventoryTransformer::class)->respond();
    }

    public function show(Inventory $inventory): JsonResponse
    {
        $this->authorize('view', $inventory);

        return fractal()->item($inventory, InventoryTransformer::class)->respond();
    }

    public function update(Request $request, Inventory $inventory): JsonResponse
    {
        $this->authorize('update', $inventory);

        // TODO: implement inventory update
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy(Inventory $inventory): JsonResponse
    {
        $this->authorize('delete', $inventory);

        $inventory->delete();

        return response()->json(null, 204);
    }
}
