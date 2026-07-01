<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaliberRequest;
use App\Models\Caliber;
use App\Transformers\CaliberTransformer;
use App\Transformers\InventoryTotalSummaryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CaliberController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Caliber::class);

        $calibers = QueryBuilder::for(Caliber::class)
            ->allowedFilters('caliber', 'label', AllowedFilter::exact('caliber_type_id'))
            ->allowedSorts('caliber', 'label')
            ->with(['caliberType', 'firearms'])
            ->get();

        return fractal($calibers, CaliberTransformer::class)->respond();
    }

    public function store(StoreCaliberRequest $request): JsonResponse
    {
        $this->authorize('create', Caliber::class);

        $caliber = Caliber::create([...$request->validated(), 'user_id' => auth()->id()]);

        return fractal()->item($caliber, CaliberTransformer::class)->respond();
    }

    public function show(Caliber $caliber): JsonResponse
    {
        $this->authorize('view', $caliber);

        $caliber->load(['caliberType', 'firearms']);

        return fractal()->item($caliber, CaliberTransformer::class)->respond();
    }

    public function total(Caliber $caliber): JsonResponse
    {
        $this->authorize('view', $caliber);

        $caliber->load(['ammunition', 'ammunition.inventories']);

        $total = ['total' => 0];

        $caliber->ammunition->groupBy('purpose_id')->each(function ($group, $key) use (&$total) {
            $group->each(function ($ammunition) use (&$total, $key) {
                $sum = $ammunition->inventories->sum('rounds');
                $total[$key] = ($total[$key] ?? 0) + $sum;
                $total['total'] += $sum;
            });
        });

        return fractal()->item($total, InventoryTotalSummaryTransformer::class)->respond();
    }

    public function update(Request $request, Caliber $caliber): JsonResponse
    {
        $this->authorize('update', $caliber);

        $validated = $request->validate([
            'caliber' => ['sometimes', 'string'],
            'label' => ['sometimes', 'string'],
            'caliber_type_id' => ['sometimes', 'integer'],
        ]);

        $caliber->update([...$validated, 'user_id' => auth()->id()]);

        return fractal()->item($caliber, CaliberTransformer::class)->respond();
    }

    public function destroy(Caliber $caliber): JsonResponse
    {
        $this->authorize('delete', $caliber);

        $caliber->delete();

        return response()->json(null, 204);
    }
}
