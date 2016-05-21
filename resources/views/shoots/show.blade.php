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
            <div class="card card-primary-outline">
                <div class="card-block card-flex">
                    <div class="rounds"><span>{{ $shoot->rounds }}</span>rnds</div>
                    <p>Fired</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Firearm</strong>:<br /><a href="{{ route('firearms.show', $shoot->firearm->id) }}">{{ $shoot->firearm->label }}</a>
                    </li>
                    <li class="list-group-item">
                        <strong>Bullet</strong>:<br /><a href="{{ route('cartridges.bullets.show', [$shoot->bullet->cartridge->id, $shoot->bullet->id]) }}">{{ $shoot->bullet->getLabel() }}</a>
                    </li>
                    <li class="list-group-item">
                        <strong>Notes</strong>:<br />{{ $shoot->notes }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
