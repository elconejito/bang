<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Ammunition;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\SessionLine;
use App\Scopes\UserScope;
use App\Transformers\InventoryTransformer;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                AllowedFilter::exact('inventory_date'),
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

        $inventory = DB::transaction(function () use ($request): Inventory {
            $extra = ['user_id' => Auth::id()];

            if ($request->boolean('is_purchase')) {
                $order = Order::create([
                    ...$request->safe()->only(['rounds', 'store_id', 'order_ref']),
                    'order_date' => $request->date('inventory_date'),
                    'total_cost' => $request->input('cost') ?? 0,
                    ...$extra,
                ]);
                $extra['order_id'] = $order->id;
                $extra['cost'] = $request->input('cost') ?? 0;
            }

            $inventory = Inventory::create([
                ...$request->safe()->only(['inventory_date', 'ammunition_id', 'rounds']),
                ...$extra,
            ]);

            Ammunition::findOrFail($request->integer('ammunition_id'))->recalculateInventory();

            return $inventory;
        });

        return fractal($inventory, InventoryTransformer::class)->respond();
    }

    public function show(Inventory $inventory): JsonResponse
    {
        $this->authorize('view', $inventory);

        return fractal()->item($inventory, InventoryTransformer::class)->respond();
    }

    public function update(UpdateInventoryRequest $request, Inventory $inventory): JsonResponse
    {
        $this->authorize('update', $inventory);

        abort_if($inventory->session_line_id !== null, 422, 'Range-session inventory must be edited from the training session.');

        DB::transaction(function () use ($request, $inventory): void {
            $inventory->update($request->safe()->only(['inventory_date', 'rounds']));

            if ($inventory->order_id !== null) {
                $order = $inventory->order;
                $order->update([
                    'order_date' => $request->date('inventory_date'),
                    'store_id' => $request->input('store_id'),
                    'order_ref' => $request->input('order_ref'),
                ]);
                $order->inventories()->update(['inventory_date' => $request->date('inventory_date')]);
                $inventory->update(['cost' => $request->input('cost') ?? 0]);
                $order->recalculateTotals();
            }

            Ammunition::findOrFail($inventory->ammunition_id)->recalculateInventory();
        });

        return fractal($inventory->refresh()->load('order.store'), InventoryTransformer::class)->respond();
    }

    public function destroy(Inventory $inventory): JsonResponse
    {
        $this->authorize('delete', $inventory);

        DB::transaction(function () use ($inventory): void {
            $order = $inventory->order;
            $ammunition = Ammunition::findOrFail($inventory->ammunition_id);
            $inventory->delete();
            $ammunition->recalculateInventory();

            if ($order !== null) {
                if ($order->inventories()->doesntExist()) {
                    $order->delete();
                } else {
                    $order->recalculateTotals();
                }
            }
        });

        return response()->json(null, 204);
    }
}
