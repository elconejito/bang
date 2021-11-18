<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaliberRequest;
use App\Models\Caliber;
use App\Models\Reference\Purpose;
use App\Repositories\Interfaces\CaliberRepository;
use App\Transformers\CaliberTransformer;
use App\Transformers\InventoryTotalSummaryTransformer;
use App\Transformers\InventoryTotalTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
     * @return JsonResponse
     */
    public function store(StoreCaliberRequest $request)
    {
        // create the new Cartridge
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $caliber = $this->caliberRepository->create($data);

        return fractal()->item($caliber, CaliberTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param $caliber_id
     *
     * @return JsonResponse
     */
    public function show($caliber_id): JsonResponse
    {
        $caliber = $this->caliberRepository->with(['caliberType', 'firearms'])->find($caliber_id);

        return fractal()->item($caliber, CaliberTransformer::class)
            ->respond();
    }

    public function total($caliber_id): JsonResponse
    {
        $caliber = $this->caliberRepository
            ->with(['ammunition', 'ammunition.inventories'])
            ->find($caliber_id);

        $total = [
            'total' => 0,
        ];

        $caliber->ammunition->groupBy('purpose_id')->each(function ($group, $key) use(&$total) {
            $group->each(function ($ammunition) use(&$total, $key) {
                $sum = $ammunition->inventories->sum('rounds');
                if (isset($total[$key])) {
                    $total[$key] += $sum;
                } else {
                    $total[$key] = $sum;
                }
                $total['total'] += $sum;
            });
        });

        return fractal()->item($total, InventoryTotalSummaryTransformer::class)->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $caliber_id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $caliber_id)
    {
        $caliber = $this->caliberRepository->update($request->all(), $caliber_id);

        return fractal()->item($caliber, CaliberTransformer::class)
            ->respond();
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
