@extends('layouts.master')

@section('title', 'Show | Range Trip')

@section('content')
    <a href="{{ route('trips.shoots.create', $trip->id) }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Shoot</a>
    <h1>Range Trip</h1>
    <p>{{ $trip->range->label }} {{ $trip->trip_date->toDateString() }}</p>
    <div class="row">
        <div class="col-sm-12">
            @if ( $trip->shoots->isEmpty() )
            <p>No Shoots yet.</p>
            @else
            <table class="table table-hover">
                <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Rounds</th>
                        <th>Firearm</th>
                        <th>Bullet</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $trip->shoots() as $shoot )
                    <tr>
                        <td scope="row">{{ $shoot->id }}</td>
                        <td>{{ $shoot->rounds }}</td>
                        <td><a href="{{ route('shootsFirearms', $shoot->firearm->id) }}">{{ $shoot->firearm->label }}</a></td>
                        <td><a href="{{ route('shootsBullets', $shoot->bullet->id) }}">{{ $shoot->bullet->manufacturer }} {{ $shoot->bullet->model }}</a></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('shoots.edit', $shoot->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i></a>
                                <a href="{{ route('shoots.destroy', $shoot->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        </div>
    </div>

@endsection
