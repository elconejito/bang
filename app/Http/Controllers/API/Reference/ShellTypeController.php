<?php

namespace App\Http\Controllers\API\Reference;

use App\Http\Controllers\Controller;
use App\Models\Reference\CaliberType;
use App\Repositories\Interfaces\ShellTypeRepository;
use App\Transformers\ShellTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShellTypeController extends Controller
{
    /**
     * @var ShellTypeRepository
     */
    protected $repository;

    public function __construct(ShellTypeRepository $shell_type_repository){
        $this->repository = $shell_type_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $caliberTypes = $this->repository->all();

        return fractal($caliberTypes, ShellTypeTransformer::class)
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
