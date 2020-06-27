<?php

namespace App\Http\Controllers;

use App\Models\Caliber;
use App\Models\Magazine;
use App\Models\Picture;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MagazineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $magazines = Magazine::all();

        return view('magazines.index', compact('magazines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('magazines.create', [ 'calibers' => Caliber::all() ]);
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
        $data = array_merge(
            $request->only([
                'label',
                'manufacturer',
                'model_name',
                'capacity',
                'serial_number',
                'id_marking',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );
        $magazine = Magazine::create($data);

        $magazine->calibers()->attach($request->input('caliber_id'));

        session()->flash('message', 'Magazine has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('MagazineController@show', $magazine->id);
    }

    /**
     * Display the specified resource.
     *
     * @param Magazine $magazine
     *
     * @return View
     */
    public function show(Magazine $magazine)
    {
        return view('magazines.show', compact('magazine'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Magazine $magazine
     *
     * @return void
     */
    public function edit(Magazine $magazine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Magazine $magazine
     *
     * @return void
     */
    public function update(Request $request, Magazine $magazine)
    {
        //
    }

    public function addPhoto(Request $request, Magazine $magazine)
    {
        // #TODO check for valid file
        // save the original photo
        $path = $request->file->store('public/images');
        $filename = str_replace('public/images/', '', $path);

        $picture = Picture::create([
            'name' => $filename,
            'filename' => $filename
        ]);

        // save the resized images
        $picture->resize();

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
     * @param Magazine $magazine
     *
     * @return void
     */
    public function destroy(Magazine $magazine)
    {
        //
    }
}
