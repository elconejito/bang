<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Repositories\Interfaces\LocationRepository;
use App\Transformers\LocationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    private LocationRepository $repository;

    public function __construct(LocationRepository $location_repository)
    {
        $this->repository = $location_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $locations = $this->repository->all();

        return fractal($locations, LocationTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLocationRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        $data = array_merge(
            $request->only([
                'label',
                'type_id',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );

        $location = $this->repository->create($data);

        return fractal()->item($location, LocationTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  Location  $location
     *
     * @return JsonResponse
     */
    public function show(Location $location): JsonResponse
    {
        return fractal()->item($location, LocationTransformer::class)
                        ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateLocationRequest  $request
     * @param  Location  $location
     *
     * @return JsonResponse
     */
    public function update(UpdateLocationRequest $request, Location $location): JsonResponse
    {
        $data = array_merge(
            $request->only([
                'label',
                'type_id',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );

        $location = $this->repository->update($data, $location->id);

        return fractal()->item($location, LocationTransformer::class)
                        ->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
