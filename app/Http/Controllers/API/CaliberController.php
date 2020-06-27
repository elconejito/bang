<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaliberRequest;
use App\Models\Caliber;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CaliberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        return [];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('calibers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCaliberRequest  $request
     *
     * @return RedirectResponse
     */
    public function store(StoreCaliberRequest $request)
    {
        // create the new Cartridge
        $caliber = Auth::user()
                         ->calibers()
                         ->create($request->all());

        session()->flash('message', 'Caliber has been added');
        session()->flash('message-type', 'success');

        return Redirect('calibers');
    }

    /**
     * Display the specified resource.
     *
     * @param Caliber $caliber
     * @return View
     */
    public function show(Caliber $caliber)
    {
        return view('calibers.show', compact('caliber'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Caliber  $caliber
     * @return View
     */
    public function edit(Caliber $caliber)
    {
        return view('calibers.edit', [ 'caliber' => $caliber ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Caliber  $caliber
     * @return RedirectResponse
     */
    public function update(Request $request, Caliber $caliber)
    {
        // Update data
        $caliber->size = $request->size;
        $caliber->label = $request->label;
        // Save it
        $caliber->save();

        session()->flash('message', 'Caliber has been saved');
        session()->flash('message-type', 'success');

        return Redirect('calibers');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Caliber  $caliber
     * @return Response
     */
    public function destroy(Caliber $caliber)
    {
        //
    }
}
