<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Firearm;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class FirearmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('firearms.index', [ 'firearms' => Firearm::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('firearms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        // create the new Cartridge
        $firearm = new Firearm();
        $firearm->label = $request->label;
        $firearm->manufacturer = $request->manufacturer;
        $firearm->model = $request->model;
        $firearm->cartridge_id = $request->cartridge_id;
        $firearm->user_id = Auth::id();

        $firearm->save();

        session()->flash('message', 'Firearm has been added');
        session()->flash('message-type', 'success');

        return Redirect('firearms');
    }

    /**
     * Display the specified resource.
     *
     * @param Firearm $firearm
     *
     * @return View
     */
    public function show(Firearm $firearm)
    {
        return view('firearms.show', [ 'firearm' => $firearm ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Firearm $firearm
     *
     * @return View
     */
    public function edit(Firearm $firearm)
    {
        return view('firearms.edit', [ 'firearm' => $firearm ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Firearm $firearm
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Request $request, Firearm $firearm)
    {
        // Update data
        $firearm->label = $request->label;
        $firearm->manufacturer = $request->manufacturer;
        $firearm->model = $request->model;
        $firearm->cartridge_id = $request->cartridge_id;

        // Save it
        $firearm->save();

        session()->flash('message', 'Firearm has been saved');
        session()->flash('message-type', 'success');

        return Redirect('firearms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Firearm $firearm
     *
     * @return void
     */
    public function destroy(Firearm $firearm)
    {
        //
    }
}
