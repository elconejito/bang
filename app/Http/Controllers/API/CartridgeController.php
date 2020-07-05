<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreCartridgeRequest;
use App\Models\Cartridge;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartridgeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('cartridges.index', [ 'cartridges' => Cartridge::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('cartridges.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCartridgeRequest $request
     * @return Response
     */
    public function store(StoreCartridgeRequest $request)
    {
        // create the new Cartridge
        $cartridge = Auth::user()
            ->cartridges()
            ->create($request->all());

        session()->flash('message', 'Cartridge has been added');
        session()->flash('message-type', 'success');

        return Redirect('cartridges');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
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
     * @return Response
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
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Find the Cartridge
        $cartridge = Cartridge::find($id);
        // Update data
        $cartridge->size = $request->size;
        $cartridge->label = $request->label;
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
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
