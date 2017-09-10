@extends('layouts.master')

@section('title', 'Show | Shoot')

@section('content')
    {!! Breadcrumbs::render('shoot', $shoot) !!}
    <div class="btn-group pull-right">
        <a href="{{ route('trips.shoots.edit', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Shoot</a>
        <a href="{{ route('trips.shoots.destroy', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
    </div>
    <h1>Shoot</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Fired</h4>
                    <div class="rounds"><span>{{ $shoot->rounds }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Firearm:</h5>
                    <p class="card-text"><a href="{{ route('firearms.show', $shoot->firearm->id) }}">{{ $shoot->firearm->label }}</a></p>
                    <h5>Bullet:</h5>
                    <p class="card-text"><a href="{{ route('cartridges.bullets.show', [$shoot->bullet->cartridge->id, $shoot->bullet->id]) }}">{{ $shoot->bullet->getLabel() }}</a></p>
                    <h5>Notes:</h5>
                    <p class="card-text">{{ $shoot->notes }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
