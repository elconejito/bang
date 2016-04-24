<?php

namespace App\Http\Controllers;

use App\Shoot;
use App\Trip;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ShootController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shoots.index', [ 'shoots' => Shoot::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($tripID)
    {
        return view('shoots.create', [ 'trip' => Trip::find($tripID) ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $tripID)
    {
        // create the new Order
        $shoot = new Shoot();
        $trip = Trip::find($tripID);

        // Get the data
        $shoot->rounds = $request->rounds;
        $shoot->firearm_id = $request->firearm_id;
        $shoot->bullet_id = $request->bullet_id;
        $shoot->trip()->associate($trip);

        // Save the Order
        $shoot->save();

        // Update inventory for the Bullet
        Bullet::updateInventory();

        session()->flash('message', 'Shoot has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('TripController@show', [ $trip->id ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($tripID, $id)
    {
        return view('shoots.show', [ 'shoot' => Shoot::find($id) ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($tripID, $id)
    {
        return view('shoots.edit', [ 'shoot' => Shoot::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tripID, $id)
    {
        // create the new Order
        $shoot = Shoot::find($id);
        $trip = Trip::find($tripID);

        // Get the data
        $shoot->rounds = $request->rounds;
        $shoot->firearm_id = $request->firearm_id;
        $shoot->bullet_id = $request->bullet_id;
        $shoot->trip()->associate($trip);

        // Save the Order
        $shoot->save();

        // Update inventory for the Bullet
        Bullet::updateInventory();

        session()->flash('message', 'Shoot has been Saved');
        session()->flash('message-type', 'success');

        return redirect()->action('TripController@show', [ $trip->id ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($tripID, $id)
    {
        //
    }

    public function showFirearms($id) {
        return view('shoots.index', [ 'shoots' => Shoot::where('firearm_id', $id)->get() ]);
    }

    public function showBullets($id) {
        return view('shoots.index', [ 'shoots' => Shoot::where('bullet_id', $id)->get() ]);
    }
}
