<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\DeleteAsset;
use App\Actions\Assets\SyncAssetPurchase;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLightRequest;
use App\Http\Requests\UpdateLightRequest;
use App\Models\Light;
use App\QueryFilters\FiltersLifecycleStatus;
use App\Transformers\LightTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->with(['color', 'firearm', 'location', 'orderAsset.order.store'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($lights, LightTransformer::class)->respond();
    }

    /**
     * @param  StoreLightRequest  $request
     * @return JsonResponse
     */
    public function store(StoreLightRequest $request, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('create', Light::class);

        $light = DB::transaction(function () use ($request, $syncAssetPurchase): Light {
            $light = Light::create([
                ...$request->safe()->except(['order_id', 'cost']),
                'user_id' => Auth::id(),
            ]);
            $syncAssetPurchase->execute($light, $request->safe()->only(['order_id', 'cost']), Auth::id());

            return $light;
        }, attempts: 3);

        $light->load(['color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($light, LightTransformer::class)->respond();
    }

    /**
     * @param  Light  $light
     * @return JsonResponse
     */
    public function show(Light $light): JsonResponse
    {
        $this->authorize('view', $light);

        $light->load(['color', 'firearm', 'location', 'orderAsset.order.store']);

        return fractal($light, LightTransformer::class)->respond();
    }

    /**
     * @param  UpdateLightRequest  $request
     * @param  Light  $light
     * @return JsonResponse
     */
    public function update(UpdateLightRequest $request, Light $light, SyncAssetPurchase $syncAssetPurchase): JsonResponse
    {
        $this->authorize('update', $light);

        if ($light->isArchived() && $request->filled('firearm_id')) {
            return response()->json(['message' => 'Unarchive this light before mounting it.', 'code' => 'archived_item_assignment_blocked'], 409);
        }

        DB::transaction(function () use ($light, $request, $syncAssetPurchase): void {
            $light->update($request->safe()->except(['order_id', 'cost']));
            $syncAssetPurchase->execute($light, $request->safe()->only(['order_id', 'cost']), Auth::id());
        }, attempts: 3);

        $light->load(['color', 'firearm', 'location', 'orderAsset.order.store']);

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
