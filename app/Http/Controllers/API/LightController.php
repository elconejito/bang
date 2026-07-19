<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLightRequest;
use App\Http\Requests\UpdateLightRequest;
use App\Models\Light;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\LightTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LightController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Light::class);

        $lights = QueryBuilder::for(Light::class)
            ->allowedFilters('manufacturer', 'label', AllowedFilter::exact('firearm_id'), AllowedFilter::custom('status', new FiltersLifecycleStatus)->default('active'))
            ->allowedSorts('manufacturer', 'label', 'lumens')
            ->with(['firearm', 'location'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($lights, LightTransformer::class)->respond();
    }

    /**
     * @param  StoreLightRequest  $request
     * @return JsonResponse
     */
    public function store(StoreLightRequest $request): JsonResponse
    {
        $this->authorize('create', Light::class);

        $light = Light::create([
            ...$request->safe()->except([]),
            'user_id' => Auth::id(),
        ]);

        $light->load(['firearm', 'location', 'purchaseStore']);

        return fractal($light, LightTransformer::class)->respond();
    }

    /**
     * @param  Light  $light
     * @return JsonResponse
     */
    public function show(Light $light): JsonResponse
    {
        $this->authorize('view', $light);

        $light->load(['firearm', 'location', 'purchaseStore']);

        return fractal($light, LightTransformer::class)->respond();
    }

    /**
     * @param  UpdateLightRequest  $request
     * @param  Light  $light
     * @return JsonResponse
     */
    public function update(UpdateLightRequest $request, Light $light): JsonResponse
    {
        $this->authorize('update', $light);

        if ($light->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this light before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }

        $light->update($request->safe()->except([]));

        $light->load(['firearm', 'location', 'purchaseStore']);

        return fractal($light, LightTransformer::class)->respond();
    }

    /**
     * @param  Light  $light
     * @return JsonResponse
     */
    public function destroy(Light $light, DeleteAsset $deleteAsset): JsonResponse
    {
        $this->authorize('delete', $light);

        $blockers = $deleteAsset->execute($light);

        if ($blockers !== []) {
            return response()->json(['message' => 'This light cannot be permanently deleted.', 'code' => 'light_delete_blocked', 'blockers' => $blockers], 409);
        }

        return response()->json(null, 204);
    }
}
