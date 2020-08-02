<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\CaliberType;
use App\Repositories\Interfaces\PrimerTypeRepository;
use App\Transformers\PrimerTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrimerTypeController extends Controller
{
    /**
     * @var PrimerTypeRepository
     */
    protected $repository;

    public function __construct(PrimerTypeRepository $primer_type_repository){
        $this->repository = $primer_type_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $caliberTypes = $this->repository->all();

        return fractal($caliberTypes, PrimerTypeTransformer::class)
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
