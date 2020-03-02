@extends('layouts.master')

@section('title', 'Show | Ammunition')

@section('content')

    <div class="row">
        <div class="col page-header">
            {!! Breadcrumbs::render('ammunitions', $ammunition) !!}
            <div class="btn-toolbar pull-right" role="toolbar">
                <div class="btn-group" role="group" aria-label="Bullet Actions">
                    <a href="{{ route('calibers.ammunitions.edit', [$ammunition->caliber->id, $ammunition->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Ammunition</a>
                    <a href="{{ route('calibers.ammunitions.destroy', [$ammunition->caliber->id, $ammunition->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </div>
            </div>
            <h1><small>{{ $ammunition->manufacturer }}</small><br />{{ $ammunition->name }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div>
                        <strong>Cartridge:</strong> {{ $ammunition->caliber->size }}<br />
                        <strong>Weight:</strong> {{ $ammunition->weight }}gr<br />
                        <strong>Purpose:</strong> {{ $ammunition->purpose->label }}<br />
                    </div>
                    <div class="rounds"><span>{{ $ammunition->inventory }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Manufacturer:</strong><br />
                        {{ $ammunition->manufacturer }}
                    </p>
                    <p class="card-text">
                        <strong>Notes:</strong><br />
                        {{ $ammunition->notes }}
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
            @if ( ! $ammunition->inventories()->get()->isEmpty() )
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Date</th>
                            <th>Rounds</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach( $ammunition->inventories()->get() as $inventory )
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
            @if ( ! $ammunition->shoots()->get()->isEmpty() )
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Date</th>
                        <th>Rounds</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $ammunition->shoots()->get() as $shoot )
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
