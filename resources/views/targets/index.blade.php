
@extends('layouts.master')

@section('title', 'Targets')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Targets',
        'breadcrumbName' => 'targets',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('targets.create'),
        'buttonRouteParams' => null,
        'buttonText' => 'Add New Target'
    ])

    <div class="row">
    @if ( $targets->isEmpty() )
        <div class="col">
            <p>No Targets yet.</p>
        </div>
    @else
        @foreach ( $targets as $target )
            <div class="col-sm-6 col-lg-4">
                <div class="card border-dark">
                    <div class="card-header">
                        <h4 class="card-title">
                            <a href="{{ route('targets.show', $target->id) }}">{{ $target->label }}</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <img class="card-img-top" src="{{ $target->picture->getPath() }}" alt="Card image cap">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Distance: <span class="badge badge-dark pull-right">{{ $target->distance }}</span></li>
                            <li class="list-group-item">Firearm: <span class="badge badge-dark pull-right">{{ $target->firearm->label }}</span></li>
                        </ul>
                    </div>
                    <div class="card-footer text-muted">
                        <a class="card-link" href="{{ route('targets.edit', $target->id) }}">Edit</a>
                        <a class="card-link" href="{{ route('targets.destroy', $target->id) }}">Delete</a>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    </div>
@endsection
