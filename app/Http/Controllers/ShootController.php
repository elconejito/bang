<?php

namespace App\Http\Controllers;

use App\Bullet;
use App\TrainingSession;
use App\Training;
use Auth;
use Illuminate\Http\Request;

class ShootController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shoots.index', [ 'shoots' => TrainingSession::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($tripID)
    {
        return view('shoots.create', [ 'trip' => Training::find($tripID) ]);
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
        $shoot = new TrainingSession();
        $trip = Training::find($tripID);
        $bullet = Bullet::find($request->bullet_id);

        // Get the data
        $shoot->rounds = $request->rounds;
        $shoot->firearm_id = $request->firearm_id;
        $shoot->user_id = Auth::id();

        // Add Relationships
        $shoot->trip()->associate($trip);
        $shoot->bullet()->associate($bullet);

        // Save the Order
        $shoot->save();

        // Update the inventory
        $bullet->inventory();

        session()->flash('message', 'Shoot has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('ShootController@show', [ $trip->id, $shoot->id ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Training $trip
     * @param TrainingSession $shoot
     *
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show(Training $trip, TrainingSession $shoot)
    {
        return view('shoots.show', compact('shoot'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($tripID, $id)
    {
        return view('shoots.edit', [ 'shoot' => TrainingSession::find($id) ]);
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
        $shoot = TrainingSession::find($id);
        $trip = Training::find($tripID);
        $bullet = Bullet::find($request->bullet_id);

        // Get the data
        $shoot->rounds = $request->rounds;
        $shoot->firearm_id = $request->firearm_id;

        $shoot->trip()->associate($trip);
        $shoot->bullet()->associate($bullet);

        // Save the Order
        $shoot->save();

        // Update the inventory
        $bullet->inventory();

        session()->flash('message', 'Shoot has been Saved');
        session()->flash('message-type', 'success');

        return redirect()->action('ShootController@show', [ $trip->id, $shoot->id ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Training $trip
     * @param TrainingSession $shoot
     *
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy(Request $request, Training $trip, TrainingSession $shoot)
    {
        if ( $shoot->delete() ) {
            $message = 'Shoot deleted.';
            $messageType = 'success';
        } else {
            $message = 'Shoot could not be deleted.';
            $messageType = 'error';
        }

        return redirect()->action('TripController@show', $trip->id)
                        ->with('message', $message)
                        ->with('message-type', $messageType);
    }

    public function showFirearms($id) {
        return view('shoots.index', [ 'shoots' => TrainingSession::where('firearm_id', $id)->get() ]);
    }

    public function showBullets($id) {
        return view('shoots.index', [ 'shoots' => TrainingSession::where('bullet_id', $id)->get() ]);
    }
}
