<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Repositories\Interfaces\TrainingRepository;
use App\Transformers\TrainingTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    /**
     * @var TrainingRepository
     */
    protected TrainingRepository $repository;

    public function __construct(TrainingRepository $repository){
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $training = $this->repository->all();

        return fractal($training, TrainingTransformer::class)
            ->respond();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('trips.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // create the new Order
        $trip = new Training();

        // Get the data
        $trip->range_id = $request->range_id;
        $trip->trip_date = $request->trip_date;
        $trip->user_id = Auth::id();

        // Save the Order
        $trip->save();

        session()->flash('message', 'Range Trip has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('TripController@show', [ $trip->id ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('trips.show', [ 'trip' => Training::find($id) ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('trips.edit', [ 'trip' => Training::find($id) ]);
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
        // create the new Order
        $trip = Training::find($id);

        // Get the data
        $trip->range_id = $request->range_id;
        $trip->trip_date = $request->trip_date;

        // Save the Order
        $trip->save();

        session()->flash('message', 'Range Trip has been Saved');
        session()->flash('message-type', 'success');

        return redirect()->action('TripController@show', [ $trip->id ]);
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

    public function showRanges($id) {
        return view('trips.index', [ 'trips' => Training::where('range_id', $id)->get() ]);
    }
}
