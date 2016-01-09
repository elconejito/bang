<?php

namespace App\Http\Controllers;

use App\Shoot;
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
    public function create()
    {
        return view('shoots.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // create the new Order
        $shoot = new Shoot();

        // Get the data
        $shoot->rounds = $request->rounds;
        $shoot->range = $request->range;
        $shoot->firearm_id = $request->firearm_id;
        $shoot->bullet_id = $request->bullet_id;
        $shoot->shoot_date = $request->shoot_date;

        // Save the Order
        $shoot->save();

        // Update inventory for the Bullet
        $shoot->bullet->updateInventory();

        session()->flash('message', 'Shoot has been added');
        session()->flash('message-type', 'success');

        return Redirect('shoots');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('shoots.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        // create the new Order
        $shoot = Shoot::find($id);

        // Get the data
        $shoot->rounds = $request->rounds;
        $shoot->range = $request->range;
        $shoot->firearm_id = $request->firearm_id;
        $shoot->bullet_id = $request->bullet_id;
        $shoot->shoot_date = $request->shoot_date;

        // Save the Order
        $shoot->save();

        // Update inventory for the Bullet
        $shoot->bullet->updateInventory();

        session()->flash('message', 'Shoot has been Saved');
        session()->flash('message-type', 'success');

        return Redirect('shoots');
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
