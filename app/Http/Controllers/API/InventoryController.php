<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryRequest;
use App\Models\Order;
use App\Repositories\Interfaces\InventoryRepository;
use App\Transformers\InventoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * @var InventoryRepository
     */
    private InventoryRepository $inventoryRepository;

    /**
     * InventoryController constructor.
     *
     * @param InventoryRepository $inventory_repository
     */
    public function __construct(InventoryRepository $inventory_repository)
    {
        $this->inventoryRepository = $inventory_repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $inventories = $this->inventoryRepository->all();

        return fractal($inventories, InventoryTransformer::class)->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreInventoryRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreInventoryRequest $request) : JsonResponse
    {
        $extra = [
            'user_id' => Auth::id(),
        ];

        // Check if there is an order
        Log::debug(__METHOD__.':'.__LINE__, [$request->all()]);
        if ($request->get('is_purchase')) {
            // Merge Request data (some fields don't have the same name), and user_id
            $orderData = array_merge(
                $request->only([
                    'rounds',
                    'store_id',
                ]),
                [
                    'order_date' => $request->get('inventory_date'),
                    'total_cost' => $request->get('cost'),
                ],
                $extra,
            );
            // Create the Order
            $order = Order::create($orderData);

            // Attach the Order info to the Inventory
            $extra['order_id'] = $order->id;
            $extra['cost'] = $request->get('cost');
        }

        $data = array_merge(
            $request->only([
                'inventory_date',
                'ammunition_id',
                'rounds',
            ]),
            $extra,
        );
        $inventory = $this->inventoryRepository->create($data);

        return fractal($inventory, InventoryTransformer::class)->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($orderID, $id)
    {
        return view('inventories.show', [ 'inventory' => Inventory::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $orderID, $id)
    {
        // create the new Order
        $inventory = Inventory::find($id);
        $order = Order::find($orderID);
        $bullet = Bullet::find($request->bullet_id);

        // Get the data
        $inventory->boxes = $request->boxes;
        $inventory->rounds_per_box = $request->rounds_per_box;
        $inventory->rounds = $request->rounds_per_box * $request->boxes;
        $inventory->cost_per_box = $request->cost_per_box;
        $inventory->cost = $request->cost_per_box * $request->boxes;

        // Make relationships
        $inventory->bullet()->associate($bullet);
        $inventory->order()->associate($order);

        // Update the totals
        $inventory->order->updateCost();
        $inventory->order->updateRounds();
        $inventory->order->save();

        // Save the Order
        $inventory->save();

        // Update inventory for this Bullet
        $bullet->inventory();

        session()->flash('message', 'Inventory has been Saved');
        session()->flash('message-type', 'success');

        return redirect()->action('InventoryController@show', [ $order->id, $inventory->id ]);
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
}
