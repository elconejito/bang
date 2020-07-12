<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaliberRequest;
use App\Models\Caliber;
use App\Repositories\Interfaces\CaliberRepository;
use App\Transformers\CaliberTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CaliberController extends Controller
{
    /**
     * @var CaliberRepository
     */
    protected $caliberRepository;

    public function __construct(CaliberRepository $caliberRepository){
        $this->caliberRepository = $caliberRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $calibers = $this->caliberRepository->with(['caliberType', 'firearms'])->all();

        return fractal($calibers, CaliberTransformer::class)
            ->respond();
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
     * @param $caliber_id
     *
     * @return JsonResponse
     */
    public function show($caliber_id)
    {
        $caliber = $this->caliberRepository->with(['caliberType', 'firearms'])->find($caliber_id);

        return fractal()->item($caliber, CaliberTransformer::class)
            ->respond();
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
