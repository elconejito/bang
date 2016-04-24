@extends('layouts.master')

@section('title', 'Range Trips')

@section('content')
    {!! Breadcrumbs::render('trips') !!}
    <a href="{{ route('trips.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Range Trip</a>
    <h1>Range Trips</h1>
    @if ( $trips->isEmpty() )
        <p>No Range Trips yet.</p>
    @else
        <table class="table table-hover">
            <thead class="thead-default">
                <tr>
                    <th>#</th>
                    <th>Range Trip Date</th>
                    <th>Range</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $trips as $trip )
                <tr>
                    <td scope="row">
                        <div class="btn-group">
                            <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-info btn-sm">View</a>
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
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
