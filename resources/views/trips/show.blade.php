@extends('layouts.master')

@section('title', 'Show | Range Trip')

@section('content')
    {!! Breadcrumbs::render('trip', $trip) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Trip Actions">
            <a href="{{ route('trips.shoots.create', $trip->id) }}" class="btn btn-success-outline"><i class="fa fa-plus"></i> Add New Shoot</a>
        </div>
        <div class="btn-group" role="group" aria-label="Trip Actions">
            <a href="{{ route('trips.edit', $trip->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Trip</a>
            <a href="{{ route('trips.destroy', $trip->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <h1>{{ $trip->range->label }} - {{ $trip->trip_date->toFormattedDateString() }}<br /><small>Range Trip</small></h1>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $trip->shoots()->get() as $shoot )
                    <tr>
                        <td scope="row">
                            <div class="btn-group">
                                <a href="{{ route('trips.shoots.show', [$trip->id, $shoot->id]) }}" class="btn btn-info btn-sm">View</a>
                                <button type="button" class="btn btn-secondary btn-sm" id="shoot-menu-{{ $shoot->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="shoot-menu-{{ $shoot->id }}">
                                    <a href="{{ route('trips.shoots.edit', [$trip->id, $shoot->id]) }}" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                    <a href="{{ route('trips.shoots.destroy', [$trip->id, $shoot->id]) }}" class="dropdown-item"><i class="fa fa-trash"></i> Delete</a>
                                </div>
                            </div>
                        </td>
                        <td>{{ $shoot->rounds }}</td>
                        <td>{{ $shoot->firearm->label }} <a href="{{ route('shootsFirearms', $shoot->firearm->id) }}"><i class="fa fa-search fa-border" aria-hidden="true"></i></a></td>
                        <td>{{ $shoot->bullet->getLabel() }} <a href="{{ route('shootsBullets', $shoot->bullet->id) }}"><i class="fa fa-search fa-border" aria-hidden="true"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        </div>
    </div>

@endsection
