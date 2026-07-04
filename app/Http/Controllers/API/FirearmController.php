<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFirearmRequest;
use App\Http\Requests\UpdateFirearmRequest;
use App\Models\Firearm;
use App\Transformers\FirearmTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class FirearmController extends Controller
{
    /**
     * Return a filtered, sorted list of the authenticated user's firearms.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Firearm::class);

        $firearms = QueryBuilder::for(Firearm::class)
            ->allowedFilters('manufacturer', 'model', 'label')
            ->allowedSorts('manufacturer', 'model', 'label')
            ->with(['calibers', 'location', 'purchaseStore', 'pictures'])
            ->defaultSort('manufacturer')
            ->get();

        return fractal($firearms, FirearmTransformer::class)->respond();
    }

    /**
     * Create a new firearm for the authenticated user.
     *
     * @param  StoreFirearmRequest  $request
     * @return JsonResponse
     */
    public function store(StoreFirearmRequest $request): JsonResponse
    {
        $this->authorize('create', Firearm::class);

        $firearm = Firearm::create([
            ...$request->safe()->except(['calibers']),
            'user_id' => Auth::id(),
        ]);
        $firearm->calibers()->sync($request->safe()->only(['calibers'])['calibers'] ?? []);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    /**
     * Return a single firearm with all relationships loaded.
     *
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function show(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        $firearm->load(['calibers', 'location', 'purchaseStore', 'pictures']);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    /**
     * Update an existing firearm's attributes and caliber assignments.
     *
     * @param  UpdateFirearmRequest  $request
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function update(UpdateFirearmRequest $request, Firearm $firearm): JsonResponse
    {
        $this->authorize('update', $firearm);

        $firearm->update($request->safe()->except(['calibers']));
        $firearm->calibers()->sync($request->safe()->only(['calibers'])['calibers'] ?? []);

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    /**
     * Delete a firearm.
     *
     * @param  Firearm  $firearm
     * @return JsonResponse
     */
    public function destroy(Firearm $firearm): JsonResponse
    {
        $this->authorize('delete', $firearm);

        $firearm->delete();

        return response()->json(null, 204);
    }
}
