<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmmunitionRequest;
use App\Http\Requests\UpdateAmmunitionRequest;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Transformers\AmmunitionTransformer;
use App\Transformers\InventoryTotalTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class AmmunitionController extends Controller
{
    public function index(Caliber $caliber): JsonResponse
    {
        $this->authorize('view', $caliber);

        $ammunition = QueryBuilder::for(Ammunition::class)
            ->allowedFilters(['manufacturer', 'label', 'purpose_id'])
            ->allowedSorts(['manufacturer', 'label'])
            ->where('caliber_id', $caliber->id)
            ->with(['purpose'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    public function store(StoreAmmunitionRequest $request, Caliber $caliber): JsonResponse
    {
        $this->authorize('view', $caliber);
        $this->authorize('create', Ammunition::class);

        $ammunition = Ammunition::create([
            ...$request->only([
                'manufacturer', 'label', 'weight', 'purpose_id',
                'shell_length_id', 'shell_type_id', 'shot_material_id',
                'ammunition_casing_id', 'ammunition_condition_id',
                'bullet_type_id', 'primer_type_id',
            ]),
            'caliber_id' => $caliber->id,
            'user_id' => Auth::id(),
        ]);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    public function show(Caliber $caliber, Ammunition $ammunition): JsonResponse
    {
        $this->authorize('view', $ammunition);

        $ammunition->load([
            'ammunitionCasing', 'ammunitionCondition', 'bulletType', 'primerType',
            'purpose', 'shellLength', 'shellType', 'shotMaterial',
        ]);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    public function total(Ammunition $ammunition): JsonResponse
    {
        $this->authorize('view', $ammunition);

        $ammunition->load('inventories');
        $total = $ammunition->inventories->sum('rounds');

        return fractal()->item($total, InventoryTotalTransformer::class)->respond();
    }

    public function update(UpdateAmmunitionRequest $request, Caliber $caliber, Ammunition $ammunition): JsonResponse
    {
        $this->authorize('update', $ammunition);

        $ammunition->update([
            ...$request->only([
                'manufacturer', 'label', 'weight', 'purpose_id',
                'shell_length_id', 'shell_type_id', 'shot_material_id',
                'ammunition_casing_id', 'ammunition_condition_id',
                'bullet_type_id', 'primer_type_id',
            ]),
            'caliber_id' => $caliber->id,
            'user_id' => Auth::id(),
        ]);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    public function destroy(Caliber $caliber, Ammunition $ammunition): JsonResponse
    {
        $this->authorize('delete', $ammunition);

        $ammunition->delete();

        return response()->json(null, 204);
    }
}
