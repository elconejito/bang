<?php

namespace App\Http\Controllers\API;

use App\Models\Store;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('stores.index', [ 'stores' => Store::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('stores.create');
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
        // Create the new Store
        $store = new Store();
        $store->label = $request->label;
        $store->user_id = Auth::id();

        // Save it
        $store->save();

        session()->flash('message', 'Store has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('StoreController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param Store $store
     *
     * @return View
     */
    public function show(Store $store)
    {
        return view('stores.show', [ 'store' => $store ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Store $store
     *
     * @return View
     */
    public function edit(Store $store)
    {
        return view('stores.edit', [ 'store' => $store ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Store $store
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Store $store)
    {
        // Update data
        $store->label = $request->label;

        // Save it
        $store->save();

        session()->flash('message', 'Store has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('StoreController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Store $store
     *
     * @return void
     */
    public function destroy(Store $store)
    {
        //
    }
}
