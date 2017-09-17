@extends('layouts.master')

@section('title', 'Show | Shoot')

@section('content')
    {!! Breadcrumbs::render('shoot', $shoot) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Shoot Actions">
            <a href="{{ route('targets.create', [ 'bullet_id' => $shoot->bullet->id, 'firearm_id' => $shoot->firearm->id, 'shoot_id' => $shoot->id, 'trip_id' => $shoot->trip->id ]) }}" class="btn btn-success-outline"><i class="fa fa-plus"></i> Add New Target</a>
        </div>
        <div class="btn-group" role="group" aria-label="Shoot Actions">
            <a href="{{ route('trips.shoots.edit', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Shoot</a>
            <a href="{{ route('trips.shoots.destroy', [$shoot->trip->id, $shoot->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
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
        <div class="col-md-4">
            <div class="card has-pictures">
                <div class="card-header">
                    <h4 class="card-title">Targets</h4>
                </div>
                <div class="card-body">
                    <div class="picture-main">
                        @if($shoot->targets->isEmpty())
                            <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid img-thumbnail" alt="No Picture Yet">
                        @else
                            <img src="{{ asset($shoot->targets->first()->picture->getPath('medium')) }}" class="img-fluid img-thumbnail" alt="{{ $shoot->targets->first()->picture->name }}">
                        @endif
                    </div>
                    <div class="pictures-thumbnails row">
                        @foreach($shoot->targets as $target)
                            <div class="thumbnail col-6 col-lg-4">
                                <img src="{{ asset($target->picture->getPath()) }}" class="img-thumbnail" alt="{{ $target->picture->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
