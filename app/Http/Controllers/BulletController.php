<?php

namespace App\Http\Controllers;

use App\Bullet;
use App\Cartridge;
use App\Purpose;
use Illuminate\Http\Request;

class BulletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($cartridge_id)
    {
        $cartridge = Cartridge::find($cartridge_id);

        $bullets = $cartridge->bullets()
                             ->where('inventory', '>', 0)
                             ->orderBy('manufacturer', 'asc')
                             ->orderBy('name', 'asc')
                             ->get();

        $bullets_inactive = $cartridge->bullets()
                                      ->where('inventory', '=', 0)
                                      ->orderBy('manufacturer', 'asc')
                                      ->orderBy('name', 'asc')
                                      ->get();
        
        return view('bullets.index', [
            'cartridge'        => $cartridge,
            'bullets'          => $bullets,
            'bullets_inactive' => $bullets_inactive,
            'sort'             => 'inventory',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($cartridge_id)
    {
        return view('bullets.create', [
                'cartridge' => Cartridge::find($cartridge_id)
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $cartridge_id)
    {
        // Get models
        $cartridge = Cartridge::find($cartridge_id);
        $purpose = Purpose::find($request->purpose_id);
        // create the new Bullet
        $bullet = new Bullet();

        // Set data
        $bullet->manufacturer = $request->manufacturer;
        $bullet->name = $request->name;
        $bullet->weight = $request->weight;
        // $bullet->notes = $request->notes;
        // Add relationships
        $bullet->purpose()->associate($purpose);
        $bullet->cartridge()->associate($cartridge);

        // Save it
        $bullet->save();

        session()->flash('message', 'Bullet has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('BulletController@show', [ $cartridge->id, $bullet->id ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($cartridge_id, $id)
    {
        return view('bullets.show', [ 'cartridge' => Cartridge::find($cartridge_id), 'bullet' => Bullet::find($id) ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($cartridge_id, $id)
    {
        return view('bullets.edit', [ 'bullet' => Bullet::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $cartridge_id, $id)
    {
        // Get models
        $bullet = Bullet::find($id);
        $cartridge = Cartridge::find($request->input('cartridge_id'));
        $purpose = Purpose::find($request->input('purpose_id'));

        // Update data
        $bullet->manufacturer = $request->input('manufacturer');
        $bullet->name = $request->input('name');
        $bullet->weight = $request->input('weight');
        // $bullet->notes = $request->input('notes');
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($cartridge_id, $id)
    {
        //
    }

    public function showCartridges($id) {
        return view('bullets.index', [
            'cartridges' => Cartridge::where('id',$id)->get(),
            'bullets' => Bullet::where('cartridge_id', $id)->get(),
            'sort' => 'inventory'
        ]);
    }

    public function showPurposes($id) {
        return view('bullets.index', [
            'cartridges' => Cartridge::all(),
            'bullets' => Bullet::where('purpose_id', $id)->get(),
            'sort' => 'inventory'
        ]);
    }

    public function showWeights($weight) {
        return view('bullets.index', [
            'cartridges' => Cartridge::all(),
            'bullets' => Bullet::where('weight', $weight)->get(),
            'sort' => 'inventory'
        ]);
    }

    public function showManufacturers($manufacturer) {
        return view('bullets.index', [
            'cartridges' => Cartridge::all(),
            'bullets' => Bullet::where('manufacturer', $manufacturer)->get(),
            'sort' => 'inventory'
        ]);
    }
}
