<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuppressorRequest;
use App\Http\Requests\UpdateSuppressorRequest;
use App\Models\Suppressor;
use App\Transformers\SuppressorTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class SuppressorController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Suppressor::class);

        $suppressors = QueryBuilder::for(Suppressor::class)
            ->allowedFilters('manufacturer', 'label', 'caliber_id', 'firearm_id')
            ->allowedSorts('manufacturer', 'label')
            ->with(['caliber', 'firearm', 'location'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($suppressors, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  StoreSuppressorRequest  $request
     * @return JsonResponse
     */
    public function store(StoreSuppressorRequest $request): JsonResponse
    {
        $this->authorize('create', Suppressor::class);

        $suppressor = Suppressor::create([
            ...$request->safe()->except([]),
            'user_id' => Auth::id(),
        ]);

        $suppressor->load(['caliber', 'firearm', 'location', 'purchaseStore']);

        return fractal($suppressor, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  Suppressor  $suppressor
     * @return JsonResponse
     */
    public function show(Suppressor $suppressor): JsonResponse
    {
        $this->authorize('view', $suppressor);

        $suppressor->load(['caliber', 'firearm', 'location', 'purchaseStore']);

        return fractal($suppressor, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  UpdateSuppressorRequest  $request
     * @param  Suppressor  $suppressor
     * @return JsonResponse
     */
    public function update(UpdateSuppressorRequest $request, Suppressor $suppressor): JsonResponse
    {
        $this->authorize('update', $suppressor);

        $suppressor->update($request->safe()->except([]));

        $suppressor->load(['caliber', 'firearm', 'location', 'purchaseStore']);

        return fractal($suppressor, SuppressorTransformer::class)->respond();
    }

    /**
     * @param  Suppressor  $suppressor
     * @return JsonResponse
     */
    public function destroy(Suppressor $suppressor): JsonResponse
    {
        $this->authorize('delete', $suppressor);

        $suppressor->delete();

        return response()->json(null, 204);
    }
}
