<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\CaliberType;
use App\Repositories\Interfaces\AmmunitionCasingRepository;
use App\Transformers\AmmunitionCasingTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AmmunitionCasingController extends Controller
{
    /**
     * @var AmmunitionCasingRepository
     */
    protected $repository;

    public function __construct(AmmunitionCasingRepository $ammunition_casing_repository){
        $this->repository = $ammunition_casing_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $ammunitionCasing = $this->repository->all();

        return fractal($ammunitionCasing, AmmunitionCasingTransformer::class)
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
     * @param  CaliberType  $caliberType
     * @return Response
     */
    public function show(CaliberType $caliberType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  CaliberType  $caliberType
     *
     * @return Response
     */
    public function update(Request $request, CaliberType $caliberType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CaliberType  $caliberType
     * @return Response
     */
    public function destroy(CaliberType $caliberType)
    {
        //
    }
}
