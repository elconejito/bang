<?php

namespace App\Http\Controllers\API;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFirearmRequest;
use App\Models\Caliber;
use App\Models\Firearm;
use App\Repositories\Interfaces\FirearmRepository;
use App\Transformers\FirearmTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class FirearmController extends Controller
{
    /**
     * @var FirearmRepository
     */
    private $firearmRepository;

    /**
     * FirearmController constructor.
     *
     * @param FirearmRepository $firearm_repository
     */
    public function __construct(FirearmRepository $firearm_repository)
    {
        $this->firearmRepository = $firearm_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $firearms = $this->firearmRepository
            ->orderBy('manufacturer', 'asc')
            ->with(['calibers'])
            ->all();

        return fractal($firearms, FirearmTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFirearmRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreFirearmRequest $request)
    {
        $data = array_merge(
            $request->only([
                'manufacturer',
                'model',
                'label',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );
        $firearm = Firearm::create($data);

        $firearm->calibers()->sync($request->input('calibers', []));

        return fractal()->item($firearm, FirearmTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param $firearm_id
     *
     * @return JsonResponse
     */
    public function show($firearm_id)
    {
        $firearm = $this->firearmRepository->with(['calibers'])->find($firearm_id);

        return fractal()->item($firearm, FirearmTransformer::class)
                        ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Firearm $firearm
     *
     * @return JsonResponse
     */
    public function update(Request $request, $firearm_id)
    {
        $data = array_merge(
            $request->only([
                'manufacturer',
                'model',
                'label',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );

        $firearm = $this->firearmRepository->find($firearm_id);

        $firearm->update($data);

        $firearm->calibers()->sync($request->input('calibers', []));

        return fractal()->item($firearm, FirearmTransformer::class)
                        ->respond();
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
