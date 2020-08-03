<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmmunitionRequest;
use App\Http\Requests\UpdateAmmunitionRequest;
use App\Repositories\Interfaces\AmmunitionRepository;
use App\Repositories\Interfaces\CaliberRepository;
use App\Transformers\AmmunitionTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class AmmunitionController extends Controller
{
    /**
     * @var AmmunitionRepository
     */
    protected $ammunitionRepository;
    /**
     * @var CaliberRepository
     */
    protected $caliberRepository;

    public function __construct(AmmunitionRepository $ammunition_repository, CaliberRepository $caliber_repository){
        $this->ammunitionRepository = $ammunition_repository;
        $this->caliberRepository = $caliber_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $caliber_id
     *
     * @return JsonResponse
     */
    public function index($caliber_id)
    {
        $ammunitions = $this->ammunitionRepository->orderBy('manufacturer', 'asc')->findWhere(['caliber_id' => $caliber_id]);

        return fractal($ammunitions, AmmunitionTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAmmunitionRequest $request
     * @param $caliber_id
     *
     * @return JsonResponse
     */
    public function store(StoreAmmunitionRequest $request, $caliber_id)
    {
        $caliber = $this->caliberRepository->find($caliber_id);

        // create the new Ammunition
        $data = array_merge(
            $request->only([
                'manufacturer',
                'label',
                'weight',
                'purpose_id',
                'shell_length_id',
                'shell_type_id',
                'shot_material_id',
                'ammunition_casing_id',
                'ammunition_condition_id',
                'bullet_type_id',
                'primer_type_id',
            ]),
            [
                'caliber_id' => $caliber->id,
                'user_id' => Auth::id(),
            ]
        );
        $ammunition = $this->ammunitionRepository->create($data);

        return fractal($ammunition, AmmunitionTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param $caliber_id
     * @param $ammunition_id
     *
     * @return JsonResponse
     */
    public function show($caliber_id, $ammunition_id)
    {
        $ammunition = $this->ammunitionRepository->find($ammunition_id);

        return fractal($ammunition, AmmunitionTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAmmunitionRequest $request
     * @param $caliber_id
     * @param $ammunition_id
     *
     * @return JsonResponse
     */
    public function update(UpdateAmmunitionRequest $request, $caliber_id, $ammunition_id)
    {
        $caliber = $this->caliberRepository->find($caliber_id);

        $data = array_merge(
            $request->only([
                'manufacturer',
                'name',
                'weight',
                'purpose_id',
                'shell_length_id',
                'shell_type_id',
                'shot_material_id',
                'ammunition_casing_id',
                'ammunition_condition_id',
                'bullet_type_id',
                'primer_type_id',
            ]),
            [
                'caliber_id' => $caliber->id,
                'user_id' => Auth::id(),
            ]
        );
        $ammunition = $this->ammunitionRepository->update($data, $ammunition_id);

        return fractal($ammunition, AmmunitionTransformer::class)
            ->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $caliber_id
     * @param $ammunition_id
     *
     * @return void
     */
    public function destroy($caliber_id, $ammunition_id)
    {
        $this->ammunitionRepository->delete($ammunition_id);
    }
}
