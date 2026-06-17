<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOpticRequest;
use App\Http\Requests\UpdateOpticRequest;
use App\Models\Optic;
use App\Transformers\OpticTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
            ->allowedFilters('manufacturer', 'label', 'optic_type', 'firearm_id')
            ->allowedSorts('manufacturer', 'label')
            ->with(['firearm', 'location'])
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

        $optic->load(['firearm', 'location', 'purchaseStore']);

        return fractal($optic, OpticTransformer::class)->respond();
    }

    /**
     * @param  Optic  $optic
     * @return JsonResponse
     */
    public function show(Optic $optic): JsonResponse
    {
        $this->authorize('view', $optic);

        $optic->load(['firearm', 'location', 'purchaseStore']);

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

        $optic->update($request->safe()->except([]));

        $optic->load(['firearm', 'location', 'purchaseStore']);

        return fractal($optic, OpticTransformer::class)->respond();
    }

    /**
     * @param  Optic  $optic
     * @return JsonResponse
     */
    public function destroy(Optic $optic): JsonResponse
    {
        $this->authorize('delete', $optic);

        $optic->delete();

        return response()->json(null, 204);
    }
}
