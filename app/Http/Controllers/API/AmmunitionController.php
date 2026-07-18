<?php

namespace App\Http\Controllers\API;

use App\Actions\Ammunition\BuildAmmunitionStats;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmmunitionRequest;
use App\Http\Requests\UpdateAmmunitionRequest;
use App\Models\Ammunition;
use App\Transformers\AmmunitionTransformer;
use App\Transformers\InventoryTotalTransformer;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AmmunitionController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Ammunition::class);

        $ammunition = QueryBuilder::for(Ammunition::class)
            ->allowedFilters(
                'manufacturer',
                'label',
                AllowedFilter::exact('purpose_id'),
                AllowedFilter::exact('caliber_id'),
                AllowedFilter::callback('in_stock', function (Builder $query, mixed $value): void {
                    if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                        $query->where('inventory', '>', 0);
                    }
                }),
            )
            ->allowedSorts('manufacturer', 'label', 'inventory')
            ->with(['caliber', 'purpose'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    /**
     * @param  StoreAmmunitionRequest  $request
     * @return JsonResponse
     */
    public function store(StoreAmmunitionRequest $request): JsonResponse
    {
        $this->authorize('create', Ammunition::class);

        $ammunition = Ammunition::create([
            ...$request->safe()->except([]),
            'user_id' => Auth::id(),
        ]);

        $ammunition->load(['caliber', 'purpose']);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    /**
     * @param  Ammunition  $ammunition
     * @return JsonResponse
     */
    public function show(Ammunition $ammunition): JsonResponse
    {
        $this->authorize('view', $ammunition);

        $ammunition->load([
            'caliber',
            'ammunitionCasing', 'ammunitionCondition', 'bulletType', 'primerType',
            'purpose', 'shellLength', 'shellType', 'shotMaterial',
        ]);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    /**
     * @param  Ammunition  $ammunition
     * @return JsonResponse
     */
    public function total(Ammunition $ammunition): JsonResponse
    {
        $this->authorize('view', $ammunition);

        $ammunition->load('inventories');
        $total = $ammunition->inventories->sum('rounds');

        return fractal()->item($total, InventoryTotalTransformer::class)->respond();
    }

    public function stats(Ammunition $ammunition, BuildAmmunitionStats $buildAmmunitionStats): JsonResponse
    {
        $this->authorize('view', $ammunition);

        return response()->json(['data' => $buildAmmunitionStats->handle($ammunition)]);
    }

    /**
     * @param  UpdateAmmunitionRequest  $request
     * @param  Ammunition  $ammunition
     * @return JsonResponse
     */
    public function update(UpdateAmmunitionRequest $request, Ammunition $ammunition): JsonResponse
    {
        $this->authorize('update', $ammunition);

        $ammunition->update($request->safe()->except([]));

        $ammunition->load(['caliber', 'purpose']);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    /**
     * @param  Ammunition  $ammunition
     * @return JsonResponse
     */
    public function destroy(Ammunition $ammunition): JsonResponse
    {
        $this->authorize('delete', $ammunition);

        $ammunition->delete();

        return response()->json(null, 204);
    }
}
