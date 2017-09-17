@extends('layouts.master')

@section('title', 'Show | Target')

@section('content')
    {!! Breadcrumbs::render('target', $target) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Target Actions">
            <a href="{{ route('targets.edit', $target->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Target</a>
            <a href="{{ route('targets.destroy', $target->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <h1><small>Target</small><br />{{ $target->label }}</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stats</h4>
                    <div class="rounds"><span>0</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Distance:</h5>
                    <p class="card-text">{{ $target->distance }}</p>
                    <h5>Group Size:</h5>
                    <p class="card-text">{{ $target->group_size }}</p>

                    @if($target->firearm()->exists())
                    <h5>Firearm:</h5>
                    <p class="card-text"><a href="{{ route('firearms.show', $target->firearm->id) }}">{{ $target->firearm->label }}</a></p>
                    @endif

                    @if($target->bullet()->exists())
                        <h5>Bullet:</h5>
                        <p class="card-text"><a href="{{ route('cartridges.bullets.show', [$target->bullet->cartridge->id, $target->bullet->id]) }}">{{ $target->bullet->getLabel() }}</a></p>
                    @endif

                    @if($target->trip()->exists())
                        <h5>Range Trip:</h5>
                        <p class="card-text"><a href="{{ route('trips.show', $target->trip->id) }}">{{ $target->trip->range->label }} - {{ $target->trip->trip_date->toFormattedDateString() }}</a></p>
                    @endif

                    @if($target->shoot()->exists())
                        <h5>Range Shoot:</h5>
                        <p class="card-text"><a href="{{ route('firearms.show', $target->shoot->id) }}">{{ $target->shoot->id }}</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card has-pictures">
                <div class="card-header">
                    <h4 class="card-title">Photo</h4>
                </div>
                <div class="card-body">
                    <div class="picture-main">
                        @if($target->picture->exists())
                            <img src="{{ asset($target->picture->getPath('medium')) }}" class="img-fluid img-thumbnail" alt="{{ $target->picture->name }}">
                        @else
                            <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid img-thumbnail" alt="No Picture Yet">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
