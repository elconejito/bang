<?php

namespace App\Http\Controllers;

use App\Picture;
use App\Target;
use App\Trip;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $targets = Target::all();

        return view('targets.index', compact('targets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $trip_id = Input::get('trip_id');
        $shoot_id = Input::get('shoot_id');
        $firearm_id = Input::get('firearm_id');
        $bullet_id = Input::get('bullet_id');

        return view('targets.create', compact('trip_id', 'shoot_id', 'firearm_id', 'bullet_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // #TODO check permission
        // save the original photo
        $path = $request->file->store('public/images');
        $filename = str_replace('public/images/', '', $path);

        $picture = Picture::create([
            'name' => $filename,
            'filename' => $filename,
            'user_id' => Auth::id()
        ]);

        // save the resized images
        $picture->resize();

        $data = $request->only([
            'label',
            'distance',
            'group_size',
            'trip_id',
            'shoot_id',
            'firearm_id',
            'bullet_id'
        ]);
        $data['picture_id'] = $picture->id;
        $data['user_id'] = Auth::id();

        $target = Target::create($data);

        session()->flash('message', 'Target has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('TargetController@show', $target->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function show(Target $target)
    {
        return view('targets.show', compact('target'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function edit(Target $target)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Target $target)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Target  $target
     * @return \Illuminate\Http\Response
     */
    public function destroy(Target $target)
    {
        //
    }
}
