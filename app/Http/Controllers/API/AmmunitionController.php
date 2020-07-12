<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAmmunitionRequest;
use App\Http\Requests\UpdateAmmunitionRequest;
use App\Models\Ammunition;
use App\Models\Caliber;
use App\Repositories\Interfaces\AmmunitionRepository;
use App\Transformers\AmmunitionTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AmmunitionController extends Controller
{
    /**
     * @var AmmunitionRepository
     */
    protected $ammunitionRepository;

    public function __construct(AmmunitionRepository $ammunitionRepository){
        $this->ammunitionRepository = $ammunitionRepository;
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
     * Show the form for creating a new resource.
     *
     * @param Caliber $caliber
     *
     * @return View
     */
    public function create(Caliber $caliber)
    {
        return view('ammunition.create', [
            'caliber' => $caliber,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAmmunitionRequest $request
     * @param Caliber $caliber
     *
     * @return RedirectResponse
     */
    public function store(StoreAmmunitionRequest $request, Caliber $caliber)
    {
        // create the new Ammunition
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
        $ammunition = Ammunition::create($data);

        session()->flash('message', 'Ammunition has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('AmmunitionController@show', [ $caliber->id, $ammunition->id ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Caliber $caliber
     * @param Ammunition $ammunition
     *
     * @return View
     */
    public function show(Caliber $caliber, Ammunition $ammunition)
    {
        return view('ammunition.show', [ 'caliber' => $caliber, 'ammunition' => $ammunition ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Caliber $caliber
     * @param Ammunition $ammunition
     *
     * @return View
     */
    public function edit(Caliber $caliber, Ammunition $ammunition)
    {
        return view('ammunition.edit', [ 'caliber' => $caliber, 'ammunition' => $ammunition ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAmmunitionRequest $request
     * @param Caliber $caliber
     * @param Ammunition $ammunition
     *
     * @return RedirectResponse
     */
    public function update(UpdateAmmunitionRequest $request, Caliber $caliber, Ammunition $ammunition)
    {
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
        $ammunition->update($data);

        session()->flash('message', 'Ammunition has been updated');
        session()->flash('message-type', 'success');

        return redirect()->action('AmmunitionController@show', [ $caliber->id, $ammunition->id ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Caliber $caliber
     * @param Ammunition $ammunition
     *
     * @return void
     */
    public function destroy(Caliber $caliber, Ammunition $ammunition)
    {
        //
    }
}
