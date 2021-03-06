@extends('layouts.master')

@section('title', 'Range Trips')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Range Trips',
        'breadcrumbName' => 'trips',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('trips.create'),
        'buttonText' => 'Add New Range Trip'
    ])

    @if ( $trips->isEmpty() )
        <p>No Range Trips yet.</p>
    @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Range</th>
                    <th>Rounds Fired</th>
                    <th>Firearms</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $trips as $trip )
                <tr>
                    <td scope="row">
                        <div class="btn-group">
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-primary btn-sm">View</a>
                            <button type="button" class="btn btn-secondary btn-sm" id="trip-menu-{{ $trip->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="trip-menu-{{ $trip->id }}">
                                <a href="{{ route('trips.edit', $trip->id) }}" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                <a href="{{ route('trips.destroy', $trip->id) }}" class="dropdown-item"><i class="fa fa-trash"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                    <td>{{ $trip->trip_date->toFormattedDateString() }}</td>
                    <td>{{ $trip->range->label }}</td>
                    <td><span class="badge badge-primary">{{ $trip->shoots->sum('rounds')  }}</span></td>
                    <td><?php $trip->shoots()->get()->unique('firearm_id')->each(function($item, $key) { echo ' <span class="badge badge-secondary">'.$item->firearm->label.'</span> '; }); ?></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $trips->links() }}
    @endif
@endsection
