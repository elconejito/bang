<?php

namespace App\Http\Controllers\API;

use App\Models\Picture;
use App\Models\Target;
use App\Models\Training;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $targets = Target::all();

        return view('targets.index', compact('targets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     *
     * @return View
     */
    public function create(Request $request)
    {
        $trip_id = $request->input('trip_id');
        $shoot_id = $request->input('shoot_id');
        $firearm_id = $request->input('firearm_id');
        $bullet_id = $request->input('bullet_id');

        return view('targets.create', compact('trip_id', 'shoot_id', 'firearm_id', 'bullet_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
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
     * @param Target $target
     *
     * @return View
     */
    public function show(Target $target)
    {
        return view('targets.show', compact('target'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Target $target
     *
     * @return void
     */
    public function edit(Target $target)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Target $target
     *
     * @return void
     */
    public function update(Request $request, Target $target)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Target $target
     *
     * @return void
     */
    public function destroy(Target $target)
    {
        //
    }
}
