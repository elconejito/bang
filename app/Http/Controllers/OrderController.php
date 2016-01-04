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
        $order->boxes = $request->boxes;
        $order->rounds_per_box = $request->rounds_per_box;
        $order->rounds = $request->rounds_per_box * $request->boxes;
        $order->cost_per_box = $request->cost_per_box;
        $order->store = $request->store;
        $order->bullet_id = $request->bullet_id;
        $order->order_date = $request->order_date;

        // Save the Order
        $order->save();

        // Update inventory for the Bullet
        $order->bullet->updateInventory();

        session()->flash('message', 'Order has been added');
        session()->flash('message-type', 'success');

        return Redirect('orders');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('orders.show');
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
        $order->boxes = $request->boxes;
        $order->rounds_per_box = $request->rounds_per_box;
        $order->rounds = $request->rounds_per_box * $request->boxes;
        $order->cost_per_box = $request->cost_per_box;
        $order->store = $request->store;
        $order->bullet_id = $request->bullet_id;
        $order->order_date = $request->order_date;

        // Save it
        $order->save();

        // Update inventory for the Bullet
        $order->bullet->updateInventory();

        session()->flash('message', 'Order has been saved');
        session()->flash('message-type', 'success');

        return Redirect('orders');
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
