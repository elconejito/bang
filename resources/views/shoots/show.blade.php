@extends('layouts.master')

@section('title', 'Show | Shoot')

@section('content')
    {!! Breadcrumbs::render('shoot', $shoot) !!}
    <div class="btn-group pull-right">
        <a href="{{ route('trips.shoots.edit', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Shoot</a>
        <a href="{{ route('trips.shoots.destroy', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
    </div>
    <h1>Show Shoot</h1>
    <div class="row">
        <div class="col-md-4">
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Rounds</strong>: <span class="label label-default pull-xs-right">{{ $shoot->rounds }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Firearm</strong>: <span class="pull-xs-right">{{ $shoot->firearm->label }}</span>
                </li>
                <li class="list-group-item">
                    <strong>Bullet</strong>:<br />{{ $shoot->bullet->manufacturer }} {{ $shoot->bullet->model }}
                </li>
            </ul>
        </div>
    </div>
@endsection
