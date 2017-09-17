@extends('layouts.master')

@section('title', 'Show | Order')

@section('content')
    {!! Breadcrumbs::render('order', $order) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Trip Actions">
            <a href="{{ route('orders.inventories.create', $order->id) }}" class="btn btn-success-outline"><i class="fa fa-plus"></i> Add New Inventory</a>
        </div>
        <div class="btn-group" role="group" aria-label="Trip Actions">
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Order</a>
            <a href="{{ route('orders.destroy', $order->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <h1><small>Order</small><br />{{ $order->order_date->toFormattedDateString() }}</h1>
    <div class="row">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    <h4>Total Rounds</h4>
                    <div class="rounds"><span>{{ $order->rounds }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Store</h5>
                    <p class="card-text">{{ $order->store->label }}</p>
                    <h5>Order Date</h5>
                    <p class="card-text">{{ $order->order_date->toFormattedDateString() }}</p>
                    <h5>Total Cost</h5>
                    <p class="card-text">{{ $order->getTotalCost() }}</p>
                    <h5>Notes</h5>
                    <p class="card-text">{{ $order->notes }}</p>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            @if ( $order->inventories->isEmpty() )
                <p>No Inventory Added Yet.</p>
            @else
                <table class="table table-hover">
                    <thead class="thead-default">
                    <tr>
                        <th>&nbsp;</th>
                        <th>Rounds</th>
                        <th>Cost</th>
                        <th>Bullet</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ( $order->inventories()->get() as $inventory )
                        <tr>
                            <td scope="row">
                                <div class="btn-group">
                                    <a href="{{ route('orders.inventories.show', [$order->id, $inventory->id]) }}" class="btn btn-info btn-sm">View</a>
                                    <button type="button" class="btn btn-secondary btn-sm" id="inventory-menu-{{ $inventory->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="inventory-menu-{{ $inventory->id }}">
                                        <a href="{{ route('orders.inventories.edit', [$order->id, $inventory->id]) }}" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                        <a href="{{ route('orders.inventories.destroy', [$order->id, $inventory->id]) }}" class="dropdown-item"><i class="fa fa-trash"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $inventory->rounds }}</td>
                            <td>${{ number_format($inventory->cost, 2) }}</td>
                            <td>
                                <a href="{{ route('cartridges.bullets.show', [$inventory->bullet->cartridge->id, $inventory->bullet->id]) }}">{{ $inventory->bullet->getLabel() }}</a><br />
                                <i>{{ $inventory->boxes }} boxes of {{ $inventory->rounds_per_box }} rnds @ ${{ $inventory->cost_per_box }}ea</i>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
