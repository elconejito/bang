<?php

namespace App\Http\Controllers;

use App\Firearm;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FirearmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('firearms.index', [ 'firearms' => Firearm::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('firearms.create');
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
        $firearm = new Firearm();
        $firearm->label = $request->label;
        $firearm->manufacturer = $request->manufacturer;
        $firearm->model = $request->model;
        $firearm->cartridge_id = $request->cartridge_id;
        $firearm->notes = $request->notes;
        $firearm->save();

        session()->flash('message', 'Firearm has been added');
        session()->flash('message-type', 'success');

        return Redirect('firearms');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('firearms.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('firearms.edit', [ 'firearm' => Firearm::find($id) ]);
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
        $firearm = Firearm::find($id);
        // Update data
        $firearm->label = $request->label;
        $firearm->manufacturer = $request->manufacturer;
        $firearm->model = $request->model;
        $firearm->cartridge_id = $request->cartridge_id;
        $firearm->notes = $request->notes;
        // Save it
        $firearm->save();

        session()->flash('message', 'Firearm has been saved');
        session()->flash('message-type', 'success');

        return Redirect('firearms');
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
