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
    <div class="card-deck">

            <div class="card">
                <div class="card-header">
                    <h4>Rounds</h4>
                    <div class="rounds"><span>{{ $inventory->rounds }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Bullet:</h5>
                    <p class="card-text"><a href="{{ route('cartridges.bullets.show', [$inventory->bullet->cartridge->id, $inventory->bullet->id]) }}">{{ $inventory->bullet->getLabel() }}</a></p>
                    <h5>Boxes:</h5>
                    <p class="card-text">{{ $inventory->boxes }}</p>
                    <h5>Rounds/Box:</h5>
                    <p class="card-text">{{ $inventory->rounds_per_box }}</p>
                    <h5>Notes:</h5>
                    <p class="card-text">{{ $inventory->notes }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Stats</h4>
                </div>
                <div class="card-body">
                    <h5>Cost/Round:</h5>
                    <p class="card-text">${{ number_format( ($inventory->cost_per_box / $inventory->rounds_per_box), 2 ) }}</p>
                    <h5>Cost/Box:</h5>
                    <p class="card-text">${{ number_format($inventory->cost_per_box, 2) }}</p>
                    <h5>Total Cost:</h5>
                    <p class="card-text">${{ number_format($inventory->cost, 2) }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Photos <span class="label label-default">0</span></h4>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid" alt="Card image cap">
                </div>
            </div>
        </div>
    </div>
@endsection
