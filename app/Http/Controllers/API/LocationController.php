<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Transformers\LocationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Location::class);

        $locations = QueryBuilder::for(Location::class)
            ->with('parentRecursive')
            ->withCount([
                'children',
                'firearms',
                'suppressors',
                'optics',
                'lights',
                'miscAccessories',
                'mounts',
                'magazines',
                'trainingSessions',
            ])
            ->allowedFilters('label', AllowedFilter::exact('location_type_id'))
            ->allowedSorts('label')
            ->defaultSort('label')
            ->get();

        return fractal($locations, LocationTransformer::class)->respond();
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $this->authorize('create', Location::class);

        $location = Location::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return fractal()->item($location, LocationTransformer::class)->respond();
    }

    public function show(Location $location): JsonResponse
    {
        $this->authorize('view', $location);

        return fractal()->item($location, LocationTransformer::class)->respond();
    }

    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $this->authorize('update', $location);

        $location->update($request->validated());

        return fractal()->item($location, LocationTransformer::class)->respond();
    }

    public function destroy(Location $location): JsonResponse
    {
        $this->authorize('delete', $location);

        if ($location->isInUse()) {
            return response()->json([
                'message' => 'This location cannot be deleted while it contains items or sublocations.',
                'code' => 'location_delete_blocked',
            ], 409);
        }

        $location->delete();

        return response()->json(null, 204);
    }
}
