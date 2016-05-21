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
        <div class="col-sm-3">
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Store</strong>:<br />{{ $order->store->label }}
                </li>
                <li class="list-group-item">
                    <strong>Order Date</strong>:<br />{{ $order->order_date->toFormattedDateString() }}
                </li>
                <li class="list-group-item">
                    <strong>Total Rounds</strong>: <span class="label label-default pull-xs-right">{{ $order->rounds }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Total Cost</strong>: <span class="label label-default pull-xs-right">{{ $order->getTotalCost() }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Notes</strong>:<br />{{ $order->notes }}
                </li>
            </ul>
        </div>
        <div class="col-sm-9">
            @if ( $order->inventories->isEmpty() )
                <p>No Inventory Added Yet.</p>
            @else
                <table class="table table-hover">
                    <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Rounds</th>
                        <th>Bullet</th>
                        <th>Boxes</th>
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
                            <td><a href="{{ route('cartridges.bullets.show', [$inventory->bullet->cartridge->id, $inventory->bullet->id]) }}">{{ $inventory->bullet->getLabel() }}</a></td>
                            <td>{{ $inventory->boxes }} boxes of {{ $inventory->rounds_per_box }} rnds @ ${{ $inventory->cost_per_box }}ea</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
