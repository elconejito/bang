@extends('layouts.master')

@section('title', $caliber->label . ' | Ammunition')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => $ammunition->getLabel(),
        'breadcrumbName' => 'ammunition',
        'breadcrumbParams' => $ammunition,
        'hasButton' => true,
        'buttonLink' => route('calibers.ammunitions.edit', [$ammunition->caliber->id, $ammunition->id]),
        'buttonRouteParams' => $ammunition,
        'buttonText' => 'Edit Ammunition'
    ])

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div>
                        <strong>Caliber:</strong> {{ $ammunition->caliber->label }}<br />
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
