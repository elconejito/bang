<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMiscAccessoryRequest;
use App\Http\Requests\UpdateMiscAccessoryRequest;
use App\Models\MiscAccessory;
use App\Transformers\MiscAccessoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MiscAccessoryController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', MiscAccessory::class);

        $misc = QueryBuilder::for(MiscAccessory::class)
            ->allowedFilters('manufacturer', 'label', 'sub_type', AllowedFilter::exact('firearm_id'))
            ->allowedSorts('manufacturer', 'label', 'sub_type')
            ->with(['firearm', 'location'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($misc, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  StoreMiscAccessoryRequest  $request
     * @return JsonResponse
     */
    public function store(StoreMiscAccessoryRequest $request): JsonResponse
    {
        $this->authorize('create', MiscAccessory::class);

        $misc = MiscAccessory::create([
            ...$request->safe()->except([]),
            'user_id' => Auth::id(),
        ]);

        $misc->load(['firearm', 'location', 'purchaseStore']);

        return fractal($misc, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  MiscAccessory  $miscAccessory
     * @return JsonResponse
     */
    public function show(MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('view', $miscAccessory);

        $miscAccessory->load(['firearm', 'location', 'purchaseStore']);

        return fractal($miscAccessory, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  UpdateMiscAccessoryRequest  $request
     * @param  MiscAccessory  $miscAccessory
     * @return JsonResponse
     */
    public function update(UpdateMiscAccessoryRequest $request, MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        $miscAccessory->update($request->safe()->except([]));

        $miscAccessory->load(['firearm', 'location', 'purchaseStore']);

        return fractal($miscAccessory, MiscAccessoryTransformer::class)->respond();
    }

    /**
     * @param  MiscAccessory  $miscAccessory
     * @return JsonResponse
     */
    public function destroy(MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('delete', $miscAccessory);

        $miscAccessory->delete();

        return response()->json(null, 204);
    }
}
