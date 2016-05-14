<?php

namespace App\Http\Controllers;

use App\Bullet;
use App\Cartridge;
use App\Purpose;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        
        return view('bullets.index', [
            'cartridge' => $cartridge,
            'bullets' => $cartridge->bullets,
            'sort' => 'inventory'
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
        $cartridge = Cartridge::find($cartridge_id);
        $purpose = Purpose::find($request->purpose_id);
        
        // create the new Bullet
        $bullet = new Bullet();
        $bullet->manufacturer = $request->manufacturer;
        $bullet->model = $request->model;
        $bullet->weight = $request->weight;
        
        $bullet->purpose()->associate($purpose);
        $bullet->cartridge()->associate($cartridge);

        $bullet->save();

        session()->flash('message', 'Bullet has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('BulletController@index', [ $cartridge->id ]);
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
        // Find the BulletType
        $bullet = Bullet::find($id);
        // Update data
        $bullet->manufacturer = $request->manufacturer;
        $bullet->model = $request->model;
        $bullet->weight = $request->weight;
        $bullet->purpose_id = $request->purpose_id;
        $bullet->cartridge_id = $request->cartridge_id;
        // Save it
        $bullet->save();

        session()->flash('message', 'Bullet has been saved');
        session()->flash('message-type', 'success');

        return Redirect('bullets');
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
