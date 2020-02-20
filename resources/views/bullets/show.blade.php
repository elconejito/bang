@extends('layouts.master')

@section('title', 'Show | Bullet')

@section('content')

    <div class="row">
        <div class="col page-header">
            {!! Breadcrumbs::render('bullet', $bullet) !!}
            <div class="btn-toolbar pull-right" role="toolbar">
                <div class="btn-group" role="group" aria-label="Bullet Actions">
                    <a href="{{ route('cartridges.bullets.edit', [$bullet->cartridge->id, $bullet->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Bullet</a>
                    <a href="{{ route('cartridges.bullets.destroy', [$bullet->cartridge->id, $bullet->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </div>
            </div>
            <h1><small>{{ $bullet->manufacturer }}</small><br />{{ $bullet->name }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div>
                        <strong>Cartridge:</strong> {{ $bullet->cartridge->size }}<br />
                        <strong>Weight:</strong> {{ $bullet->weight }}gr<br />
                        <strong>Purpose:</strong> {{ $bullet->purpose->label }}<br />
                    </div>
                    <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Manufacturer:</strong><br />
                        {{ $bullet->manufacturer }}
                    </p>
                    <p class="card-text">
                        <strong>Notes:</strong><br />
                        {{ $bullet->notes }}
                    </p>
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
        <div class="col-md-4">
            <h4>Orders:</h4>
            @if ( ! $bullet->inventories()->get()->isEmpty() )
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Date</th>
                            <th>Rounds</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach( $bullet->inventories()->get() as $inventory )
                        <tr>
                            <td><a href="{{ route('orders.show', $inventory->order->id) }}" class="btn btn-info btn-sm">View</a></td>
                            <td>{{ $inventory->order->order_date->toFormattedDateString() }}</td>
                            <td>{{ $inventory->rounds }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>None Yet.</p>
            @endif
        </div>
        <div class="col-md-4">
            <h4>Range Trips:</h4>
            @if ( ! $bullet->shoots()->get()->isEmpty() )
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Date</th>
                        <th>Rounds</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $bullet->shoots()->get() as $shoot )
                        <tr>
                            <td><a href="{{ route('trips.show', $shoot->trip->id) }}" class="btn btn-info btn-sm">View</a></td>
                            <td>{{ $shoot->trip->trip_date->toFormattedDateString() }}</td>
                            <td>{{ $shoot->rounds }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p>None Yet.</p>
            @endif
        </div>
    </div>

@endsection
