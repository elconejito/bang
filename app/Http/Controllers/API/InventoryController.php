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
use Illuminate\Contracts\Database\Eloquent\Builder;
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

        $perPage = min((int) request('per_page', 50), 200);

        $paginator = QueryBuilder::for(Inventory::class)
            ->allowedFilters(
                AllowedFilter::exact('ammunition_id'),
                'inventory_date',
                AllowedFilter::callback('type', function (Builder $query, mixed $value): void {
                    match (strtoupper((string) $value)) {
                        'BUY' => $query->whereNotNull('order_id'),
                        'FIRED' => $query->whereNotNull('session_line_id'),
                        'ADJUST' => $query->whereNull('order_id')->whereNull('session_line_id'),
                        default => $query,
                    };
                }),
            )
            ->allowedSorts('inventory_date', 'rounds')
            ->defaultSort('-inventory_date', 'rounds')
            ->with(['order.store'])
            ->paginate($perPage);

        $inventories = $paginator->getCollection();

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

        $transformer = new InventoryTransformer;

        // Build a running-balance map for the filtered ammo (lightweight — IDs + rounds only)
        $balanceMap = [];
        if ($ammoId = request('filter.ammunition_id') ?? request('filter[ammunition_id]')) {
            $allRounds = Inventory::where('ammunition_id', $ammoId)
                ->orderBy('inventory_date')
                ->orderBy('id')
                ->get(['id', 'rounds']);

            $running = 0;
            foreach ($allRounds as $inv) {
                $running += $inv->rounds;
                $balanceMap[$inv->id] = $running;
            }
        }

        $items = $inventories->map(function ($inv) use ($transformer, $balanceMap) {
            $data = $transformer->transform($inv);
            $data['balance'] = $balanceMap[$inv->id] ?? null;

            return $data;
        });

        return response()->json([
            'data' => $items->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StoreInventoryRequest $request): JsonResponse
    {
        $this->authorize('create', Inventory::class);

        $extra = ['user_id' => Auth::id()];

        Log::debug(__METHOD__.':'.__LINE__, [$request->all()]);
        if ($request->get('is_purchase')) {
            $order = Order::create([
                ...$request->only(['rounds', 'store_id', 'order_ref']),
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
