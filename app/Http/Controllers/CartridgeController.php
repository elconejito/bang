<?php

namespace App\Http\Controllers;

use App\Cartridge;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CartridgeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cartridges.index', [ 'cartridges' => Cartridge::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cartridges.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // create the new Cartridge
        $cartridge = new Cartridge();
        $cartridge->size = $request->size;
        $cartridge->save();

        session()->flash('message', 'Cartridge has been added');
        session()->flash('message-type', 'success');

        return Redirect('cartridges');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cartridge = Cartridge::find($id);
        return view('cartridges.show', compact('cartridge'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('cartridges.edit', [ 'cartridge' => Cartridge::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the Cartridge
        $cartridge = Cartridge::find($id);
        // Update data
        $cartridge->size = $request->size;
        // Save it
        $cartridge->save();

        session()->flash('message', 'Cartridge has been saved');
        session()->flash('message-type', 'success');

        return Redirect('cartridges');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
