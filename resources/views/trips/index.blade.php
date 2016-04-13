@extends('layouts.master')

@section('title', 'Range Trips')

@section('content')
    <a href="{{ route('trips.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New</a>
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
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $trips as $trip )
                <tr>
                    <td scope="row">{{ $trip->id }}</td>
                    <td><a href="{{ route('trips.show', $trip->id) }}">{{ $trip->trip_date->toDateString() }}</a></td>
                    <td><a href="{{ route('trips.show', $trip->id) }}">{{ $trip->range->label }}</a></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('trips.edit', $trip->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i></a>
                            <a href="{{ route('trips.destroy', $trip->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
