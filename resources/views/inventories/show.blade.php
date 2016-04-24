@extends('layouts.master')

@section('title', 'Show | Inventory')

@section('content')
    {!! Breadcrumbs::render('inventory', $inventory) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Trip Actions">
            <a href="{{ route('orders.inventories.edit', [$inventory->order->id, $inventory->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Inventory</a>
            <a href="{{ route('orders.inventories.destroy', [$inventory->order->id, $inventory->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <h1>Inventory</h1>
    <div class="row">
        <div class="col-sm-4">
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Boxes</strong>: <span class="label label-default pull-xs-right">{{ $inventory->boxes }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Rounds/Box</strong>: <span class="label label-default pull-xs-right">{{ $inventory->rounds_per_box }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Cost/Box</strong>: <span class="label label-default pull-xs-right">{{ $inventory->cost_per_box }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Total Rounds</strong>: <span class="label label-default pull-xs-right">{{ $inventory->rounds }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Total Cost</strong>: <span class="label label-default pull-xs-right">{{ $inventory->cost }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Bullet</strong>: <br />{{ $inventory->bullet->getLabel() }}
                </li>
            </ul>
        </div>
    </div>
@endsection
