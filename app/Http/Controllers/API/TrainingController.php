<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingSessionRequest;
use App\Models\TrainingSession;
use App\Repositories\Interfaces\InventoryRepository;
use App\Repositories\Interfaces\TrainingRepository;
use App\Transformers\TrainingSessionTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    /**
     * @var TrainingRepository
     */
    protected TrainingRepository $repository;
    protected InventoryRepository $inventoryRepository;

    public function __construct(TrainingRepository $repository, InventoryRepository $inventory_repository){
        $this->repository = $repository;
        $this->inventoryRepository = $inventory_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $training = $this->repository->all();

        return fractal($training, TrainingSessionTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function store(StoreTrainingSessionRequest $request): JsonResponse
    {
        $data = array_merge(
            $request->only([
                'label',
                'description',
                'session_date',
                'location_id',
            ]),
            [
                'user_id' => Auth::id(),
            ]
        );

        try {
            DB::beginTransaction();

            /** @var TrainingSession $trainingSession */
            $trainingSession = $this->repository->create($data);
            if ($request->has('inventories')) {

                foreach ($request->get('inventories') as $inventory) {
                    // loop and add user_id, ts_id
                    $data = array_merge(
                        $inventory,
                        [
                            'user_id' => Auth::id(),
                            'training_session_id' => $trainingSession->id,
                        ],
                    );
                    // insert into inventories repository
                    $this->inventoryRepository->create($data);
                }
            }

            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ], 500);
        }

        return fractal()->item($trainingSession, TrainingSessionTransformer::class)
                        ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $trainingSession = $this->repository->find($id);

        return fractal()->item($trainingSession, TrainingSessionTransformer::class)
                        ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     *
     * @return Response
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
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
