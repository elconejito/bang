@extends('layouts.master')

@section('title', 'Shoots')

@section('content')
    <div class="container">
        <a href="{{ route('shoots.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New</a>
        <h1>Shoots</h1>
        @if ( $shoots->isEmpty() )
            <p>No Shoots yet.</p>
            <p><a href="{{ route('shoots.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
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
                        <td>{{ $shoot->shoot_date->toDateString() }}</td>
                        <td>{{ $shoot->rounds }}</td>
                        <td><a href="{{ route('shootsRanges', $shoot->range->id) }}">{{ $shoot->range->label }}</a></td>
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
    </div><!-- /.container -->
@endsection
