<?php

namespace App\Http\Controllers\API;

use App\Bullet;
use App\Inventory;
use App\Order;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('inventories.index', [ 'inventories' => Inventory::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($inventoryID)
    {
        return view('inventories.create', [ 'order' => Order::find($inventoryID) ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $orderID)
    {
        // create the new Order
        $inventory = new Inventory();
        $order = Order::find($orderID);
        $bullet = Bullet::find($request->bullet_id);

        // Get the data
        $inventory->boxes = $request->boxes;
        $inventory->rounds_per_box = $request->rounds_per_box;
        $inventory->rounds = $request->rounds_per_box * $request->boxes;
        $inventory->cost_per_box = $request->cost_per_box;
        $inventory->cost = $request->cost_per_box * $request->boxes;
        $inventory->user_id = Auth::id();

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

        session()->flash('message', 'Inventory has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('InventoryController@show', [ $order->id, $inventory->id ]);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($orderID, $id)
    {
        return view('inventories.edit', [ 'inventory' => Inventory::find($id) ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
