@extends('layouts.master')

@section('title', 'Shoots')

@section('content')
    <div class="container">
        <h1>Shoots</h1>
        @if ( $shoots->isEmpty() )
            <p>No Shoots yet.</p>
        @else
            <table class="table table-hover">
                <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Shoot Date</th>
                        <th>Rounds</th>
                        <th>Range</th>
                        <th>Firearm</th>
                        <th>Bullet</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $shoots as $shoot )
                    <tr>
                        <td scope="row">{{ $shoot->id }}</td>
                        <td><a href="{{ route('trips.show', $shoot->trip->id) }}">{{ $shoot->trip->trip_date->toDateString() }}</a></td>
                        <td>{{ $shoot->rounds }}</td>
                        <td>{{ $shoot->trip->range->label }}</td>
                        <td><a href="{{ route('shootsFirearms', $shoot->firearm->id) }}">{{ $shoot->firearm->label }}</a></td>
                        <td><a href="{{ route('shootsBullets', $shoot->bullet->id) }}">{{ $shoot->bullet->manufacturer }} {{ $shoot->bullet->model }}</a></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('trips.shoots.edit', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i></a>
                                <a href="{{ route('trips.shoots.destroy', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div><!-- /.container -->
@endsection
