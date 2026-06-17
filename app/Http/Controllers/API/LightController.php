<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLightRequest;
use App\Http\Requests\UpdateLightRequest;
use App\Models\Light;
use App\Transformers\LightTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
            ->allowedFilters('manufacturer', 'label', 'firearm_id')
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

        $light->update($request->safe()->except([]));

        $light->load(['firearm', 'location', 'purchaseStore']);

        return fractal($light, LightTransformer::class)->respond();
    }

    /**
     * @param  Light  $light
     * @return JsonResponse
     */
    public function destroy(Light $light): JsonResponse
    {
        $this->authorize('delete', $light);

        $light->delete();

        return response()->json(null, 204);
    }
}
