<?php

namespace App\Http\Controllers;

use App\Magazine;
use App\Picture;
use Illuminate\Http\Request;

class MagazineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $magazines = Magazine::all();

        return view('magazines.index', compact('magazines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('magazines.create');
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
        $data = $request->only([
            'label',
            'manufacturer',
            'model_name',
            'capacity',
            'serial_number',
            'id_marking',
            'cartridge_id',
        ]);
        $magazine = Magazine::create($data);

        session()->flash('message', 'Magazine has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('MagazineController@show', $magazine->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Magazine  $magazine
     * @return \Illuminate\Http\Response
     */
    public function show(Magazine $magazine)
    {
        // $magazine = Magazine::findOrFail($magazine);

        return view('magazines.show', compact('magazine'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Magazine  $magazine
     * @return \Illuminate\Http\Response
     */
    public function edit(Magazine $magazine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Magazine  $magazine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Magazine $magazine)
    {
        //
    }

    public function addPhoto(Request $request, Magazine $magazine)
    {
        // #TODO check for valid file
        // save the original photo
        $path = $request->file->store('images');
        $picture = Picture::create([
            'name' => ltrim($path, 'images/'),
            'filename' => $path
        ]);

        // save the resized photo

        // attach to the magazine
        $magazine->pictures()->attach($picture);

        return response()->json([
            'message' => 'Successfully added Picture to Magazine',
            'data' => [
                'path' => $path,
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Magazine  $magazine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Magazine $magazine)
    {
        //
    }
}
