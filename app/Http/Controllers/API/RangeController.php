<?php

namespace App\Http\Controllers\API;

use App\Models\Range;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('ranges.index', [ 'ranges' => Range::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('ranges.create');
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
        // Create the new Range
        $range = new Range();
        $range->label = $request->label;
        $range->user_id = Auth::id();

        // Save it
        $range->save();

        session()->flash('message', 'Range has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('RangeController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param Range $range
     *
     * @return View
     */
    public function show(Range $range)
    {
        return view('ranges.show', [ 'range' => $range ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Range $range
     *
     * @return View
     */
    public function edit(Range $range)
    {
        return view('ranges.edit', [ 'range' => $range ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Range $range
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Range $range)
    {
        // Update data
        $range->label = $request->label;

        // Save it
        $range->save();

        session()->flash('message', 'Range has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('RangeController@index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Range $range
     *
     * @return void
     */
    public function destroy(Range $range)
    {
        //
    }
}
