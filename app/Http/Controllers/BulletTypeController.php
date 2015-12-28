<?php

namespace App\Http\Controllers;

use App\BulletType;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BulletTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('bullet_types.index', [ 'bullet_types' => BulletType::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bullet_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // create the new BulletType
        $bulletType = new BulletType();
        $bulletType->size = $request->size;
        $bulletType->save();

        session()->flash('message', 'Bullet Type has been added');
        session()->flash('message-type', 'success');

        return Redirect('bullet-type');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('bullet_types.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('bullet_types.edit', [ 'bullet_type' => BulletType::find($id) ]);
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
        // Find the BulletType
        $bulletType = BulletType::find($id);
        // Update data
        $bulletType->size = $request->size;
        // Save it
        $bulletType->save();

        session()->flash('message', 'Bullet Type has been saved');
        session()->flash('message-type', 'success');

        return Redirect('bullet-type');
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
