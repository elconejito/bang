<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Transformers\LocationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = QueryBuilder::for(Location::class)
            ->allowedFilters(['label', 'location_type_id'])
            ->allowedSorts(['label'])
            ->get();

        return fractal($locations, LocationTransformer::class)->respond();
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        $location = Location::create([
            ...$request->only(['label', 'description', 'location_type_id']),
            'user_id' => Auth::id(),
        ]);

        return fractal()->item($location, LocationTransformer::class)->respond();
    }

    public function show(Location $location): JsonResponse
    {
        return fractal()->item($location, LocationTransformer::class)->respond();
    }

    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $location->update([
            ...$request->only(['label', 'description', 'location_type_id']),
            'user_id' => Auth::id(),
        ]);

        return fractal()->item($location, LocationTransformer::class)->respond();
    }

    public function destroy(Location $location): JsonResponse
    {
        $location->delete();

        return response()->json(null, 204);
    }
}
