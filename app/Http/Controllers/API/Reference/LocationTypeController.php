<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\LocationType;
use App\Repositories\Interfaces\LocationTypeRepository;
use App\Transformers\LocationTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocationTypeController extends Controller
{
    /**
     * @var LocationTypeRepository
     */
    protected LocationTypeRepository $repository;

    public function __construct(LocationTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $locationTypes = $this->repository->all();

        return fractal($locationTypes, LocationTypeTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  LocationType  $locationType
     * @return Response
     */
    public function show(LocationType $locationType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  LocationType  $locationType
     *
     * @return Response
     */
    public function update(Request $request, LocationType $locationType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  LocationType  $locationType
     * @return Response
     */
    public function destroy(LocationType $locationType)
    {
        //
    }
}
