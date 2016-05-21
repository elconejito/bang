<?php

namespace App\Http\Controllers;

use App\Bullet;
use App\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('orders.index', [ 'orders' => Order::all() ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('orders.create');
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
        $order = new Order();

        // Get the data
        $order->store_id = $request->store_id;
        $order->order_date = $request->order_date;
        $order->notes = $request->notes;

        // Update the totals
        $order->updateCost();
        $order->updateRounds();

        // Save the Order
        $order->save();

        // Update inventory for all Bullets
        Bullet::updateInventory();

        session()->flash('message', 'Order has been added');
        session()->flash('message-type', 'success');

        return redirect()->action('OrderController@show', [ $order->id ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('orders.show', [ 'order' => Order::find($id) ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('orders.edit', [ 'order' => Order::find($id) ]);
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
        // Find the Order
        $order = Order::find($id);

        // Update the data
        $order->store_id = $request->store_id;
        $order->order_date = $request->order_date;
        $order->notes = $request->notes;

        // Update the totals
        $order->updateCost();
        $order->updateRounds();

        // Save it
        $order->save();

        // Update inventory for all Bullets
        Bullet::updateInventory();

        session()->flash('message', 'Order has been saved');
        session()->flash('message-type', 'success');

        return redirect()->action('OrderController@show', [ $order->id ]);
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

    public function showStores($id) {
        return view('orders.index', [ 'orders' => Order::where('store_id', $id)->get() ]);
    }

    public function showBullets($id) {
        return view('orders.index', [ 'orders' => Order::where('bullet_id', $id)->get() ]);
    }
}
