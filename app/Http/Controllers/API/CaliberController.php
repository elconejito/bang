<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaliberRequest;
use App\Models\Caliber;
use App\Transformers\CaliberTransformer;
use App\Transformers\InventoryTotalSummaryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class CaliberController extends Controller
{
    public function index(): JsonResponse
    {
        $calibers = QueryBuilder::for(Caliber::class)
            ->allowedFilters(['caliber', 'label', 'caliber_type_id'])
            ->allowedSorts(['caliber', 'label'])
            ->with(['caliberType', 'firearms'])
            ->get();

        return fractal($calibers, CaliberTransformer::class)->respond();
    }

    public function store(StoreCaliberRequest $request): JsonResponse
    {
        $caliber = Caliber::create([...$request->validated(), 'user_id' => auth()->id()]);

        return fractal()->item($caliber, CaliberTransformer::class)->respond();
    }

    public function show(int $caliber_id): JsonResponse
    {
        $caliber = Caliber::with(['caliberType', 'firearms'])->findOrFail($caliber_id);

        return fractal()->item($caliber, CaliberTransformer::class)->respond();
    }

    public function total(int $caliber_id): JsonResponse
    {
        $caliber = Caliber::with(['ammunition', 'ammunition.inventories'])->findOrFail($caliber_id);

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

    public function update(Request $request, int $caliber_id): JsonResponse
    {
        $caliber = Caliber::findOrFail($caliber_id);
        $caliber->update([...$request->all(), 'user_id' => auth()->id()]);

        return fractal()->item($caliber, CaliberTransformer::class)->respond();
    }

    public function destroy(int $caliber_id): JsonResponse
    {
        Caliber::findOrFail($caliber_id)->delete();

        return response()->json(null, 204);
    }
}
