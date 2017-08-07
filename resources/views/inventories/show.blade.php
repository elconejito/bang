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
            <div class="card card-primary-outline">
                <div class="card-block card-flex">
                    <div class="rounds"><span>{{ $inventory->rounds }}</span>rnds</div>
                    <p>Rounds</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Bullet</strong>: <br /><a href="{{ route('cartridges.bullets.show', [$inventory->bullet->cartridge->id, $inventory->bullet->id]) }}">{{ $inventory->bullet->getLabel() }}</a>
                    </li>
                    <li class="list-group-item">
                        <strong>Boxes</strong>: <span class="label label-default pull-xs-right">{{ $inventory->boxes }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Rounds/Box</strong>: <span class="label label-default pull-xs-right">{{ $inventory->rounds_per_box }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Notes</strong>: <br />{{ $inventory->notes }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-primary-outline">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Cost/Round</strong>: <span class="label label-default pull-xs-right">${{ number_format( ($inventory->cost_per_box / $inventory->rounds_per_box), 2 ) }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Cost/Box</strong>: <span class="label label-default pull-xs-right">${{ number_format($inventory->cost_per_box, 2) }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Total Cost</strong>: <span class="label label-default pull-xs-right">${{ number_format($inventory->cost, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-primary-outline">
                <div class="card-block">
                    <h4 class="card-title">Photos <span class="label label-default">0</span></h4>
                </div>
                <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid" alt="Card image cap">
            </div>
        </div>
    </div>
@endsection
