<?php

namespace App\Http\Controllers;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Cartridge;
use App\Models\Purpose;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AmmunitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Caliber $caliber
     *
     * @return View
     */
    public function index(Caliber $caliber)
    {
        $ammunitions = $caliber->ammunition()
                             ->where('inventory', '>', 0)
                             ->orderBy('manufacturer', 'asc')
                             ->orderBy('name', 'asc')
                             ->get();

        $ammunitions_inactive = $caliber->ammunition()
                                      ->where('inventory', '=', 0)
                                      ->orderBy('manufacturer', 'asc')
                                      ->orderBy('name', 'asc')
                                      ->get();

        return view('ammunition.index', [
            'caliber'           => $caliber,
            'ammunitions'          => $ammunitions,
            'ammunitions_inactive' => $ammunitions_inactive,
            'sort'                => 'inventory',
        ]);
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
     * @param Request $request
     * @param Caliber $caliber
     *
     * @return RedirectResponse
     */
    public function store(Request $request, Caliber $caliber)
    {
        // create the new Bullet
        $data = array_merge(
            $request->only([
                'manufacturer',
                'name',
                'weight',
                'purpose_id',
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
     * @param  int  $id
     * @return Response
     */
    public function edit($cartridge_id, $id)
    {
        return view('ammunition.edit', [ 'bullet' => Ammunition::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $cartridge_id, $id)
    {
        // Get models
        $bullet = Ammunition::find($id);
        $cartridge = Cartridge::find($request->input('cartridge_id'));
        $purpose = Purpose::find($request->input('purpose_id'));

        // Update data
        $bullet->manufacturer = $request->input('manufacturer');
        $bullet->name = $request->input('name');
        $bullet->weight = $request->input('weight');

        // Update relationships
        $bullet->purpose()->associate($purpose);
        $bullet->cartridge()->associate($cartridge);

        // Save it
        $bullet->save();

        session()->flash('message', 'Bullet has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('BulletController@show', [ $cartridge->id, $bullet->id ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($cartridge_id, $id)
    {
        //
    }

    public function showCartridges($id) {
        return view('bullets.index', [
            'cartridges' => Cartridge::where('id',$id)->get(),
            'bullets' => Ammunition::where('cartridge_id', $id)->get(),
            'sort' => 'inventory'
        ]);
    }

    public function showPurposes($id) {
        return view('bullets.index', [
            'cartridges' => Cartridge::all(),
            'bullets' => Ammunition::where('purpose_id', $id)->get(),
            'sort' => 'inventory'
        ]);
    }

    public function showWeights($weight) {
        return view('bullets.index', [
            'cartridges' => Cartridge::all(),
            'bullets' => Ammunition::where('weight', $weight)->get(),
            'sort' => 'inventory'
        ]);
    }

    public function showManufacturers($manufacturer) {
        return view('bullets.index', [
            'cartridges' => Cartridge::all(),
            'bullets' => Ammunition::where('manufacturer', $manufacturer)->get(),
            'sort' => 'inventory'
        ]);
    }
}
