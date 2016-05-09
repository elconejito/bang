<?php

namespace App\Http\Controllers;

use App\Purpose;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PurposeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('purposes.index', [ 'purposes' => Purpose::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('purposes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create the new Purpose
        $purpose = new Purpose();
        $purpose->label = $request->label;
        // Save it
        $purpose->save();

        session()->flash('message', 'Purpose has been saved');
        session()->flash('message-type', 'success');

        return Redirect('purposes');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('purposes.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('purposes.edit', [ 'purpose' => Purpose::find($id) ]);
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
        // Find the Purpose
        $purpose = Purpose::find($id);
        // Update data
        $purpose->label = $request->label;
        // Save it
        $purpose->save();

        session()->flash('message', 'Purpose has been saved');
        session()->flash('message-type', 'success');
        
        return redirect()->action('PurposeController@show', [ $purpose->id ]);
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
