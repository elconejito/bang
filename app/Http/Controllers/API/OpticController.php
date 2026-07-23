<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpticRequest;
use App\Http\Requests\UpdateOpticRequest;
use App\Models\Optic;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\OpticTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OpticController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Optic::class);

        $optics = QueryBuilder::for(Optic::class)
            ->allowedFilters('manufacturer', 'label', 'optic_type', AllowedFilter::exact('firearm_id'), AllowedFilter::custom('status', new FiltersLifecycleStatus)->default('active'))
            ->allowedSorts('manufacturer', 'label')
            ->with(['color', 'firearm', 'location'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($optics, OpticTransformer::class)->respond();
    }

    /**
     * @param  StoreOpticRequest  $request
     * @return JsonResponse
     */
    public function store(StoreOpticRequest $request): JsonResponse
    {
        $this->authorize('create', Optic::class);

        $optic = Optic::create([
            ...$request->safe()->except([]),
            'user_id' => Auth::id(),
        ]);

        $optic->load(['color', 'firearm', 'location', 'purchaseStore']);

        return fractal($optic, OpticTransformer::class)->respond();
    }

    /**
     * @param  Optic  $optic
     * @return JsonResponse
     */
    public function show(Optic $optic): JsonResponse
    {
        $this->authorize('view', $optic);

        $optic->load(['color', 'firearm', 'location', 'purchaseStore']);

        return fractal($optic, OpticTransformer::class)->respond();
    }

    /**
     * @param  UpdateOpticRequest  $request
     * @param  Optic  $optic
     * @return JsonResponse
     */
    public function update(UpdateOpticRequest $request, Optic $optic): JsonResponse
    {
        $this->authorize('update', $optic);

        if ($optic->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this optic before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }

        $optic->update($request->safe()->except([]));

        $optic->load(['color', 'firearm', 'location', 'purchaseStore']);

        return fractal($optic, OpticTransformer::class)->respond();
    }

    /**
     * @param  Optic  $optic
     * @return JsonResponse
     */
    public function destroy(Optic $optic, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $optic);

        $blockers = $deleteAsset->execute($optic);

        if ($blockers !== []) {
            return response()->json(['message' => 'This optic cannot be permanently deleted.', 'code' => 'optic_delete_blocked', 'blockers' => $blockers], 409);
        }

        return response()->json(null, 204);
    }
}
