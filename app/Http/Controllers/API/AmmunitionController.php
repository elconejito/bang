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
    public function index(int $caliber_id): JsonResponse
    {
        $ammunition = QueryBuilder::for(Ammunition::class)
            ->allowedFilters(['manufacturer', 'label', 'purpose_id'])
            ->allowedSorts(['manufacturer', 'label'])
            ->where('caliber_id', $caliber_id)
            ->with(['purpose'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    public function store(StoreAmmunitionRequest $request, int $caliber_id): JsonResponse
    {
        $caliber = Caliber::findOrFail($caliber_id);

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

    public function show(int $caliber_id, int $ammunition_id): JsonResponse
    {
        $ammunition = Ammunition::with([
            'ammunitionCasing', 'ammunitionCondition', 'bulletType', 'primerType',
            'purpose', 'shellLength', 'shellType', 'shotMaterial',
        ])->findOrFail($ammunition_id);

        return fractal($ammunition, AmmunitionTransformer::class)->respond();
    }

    public function total(int $ammunition_id): JsonResponse
    {
        $ammunition = Ammunition::with(['inventories'])->findOrFail($ammunition_id);
        $total = $ammunition->inventories->sum('rounds');

        return fractal()->item($total, InventoryTotalTransformer::class)->respond();
    }

    public function update(UpdateAmmunitionRequest $request, int $caliber_id, int $ammunition_id): JsonResponse
    {
        $caliber = Caliber::findOrFail($caliber_id);
        $ammunition = Ammunition::findOrFail($ammunition_id);

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

    public function destroy(int $caliber_id, int $ammunition_id): JsonResponse
    {
        Ammunition::findOrFail($ammunition_id)->delete();

        return response()->json(null, 204);
    }
}
