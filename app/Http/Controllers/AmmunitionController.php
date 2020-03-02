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
        // create the new Ammunition
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
     * @param Request $request
     * @param Caliber $caliber
     * @param Ammunition $ammunition
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Caliber $caliber, Ammunition $ammunition)
    {
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
