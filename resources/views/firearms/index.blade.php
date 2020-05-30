@extends('layouts.master')

@section('title', 'Firearms')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Firearms',
        'breadcrumbName' => 'firearms',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('firearms.create'),
        'buttonRouteParams' => null,
        'buttonText' => 'Add New Firearm',
        'buttonTextIcon' => 'fas fa-plus'
    ])

    <div class="row">
    @if ( $firearms->isEmpty() )
        <div class="col">
            <p>No Firearms yet.</p>
        </div>
    @else
        @foreach ( $firearms as $firearm )
        <div class="col-sm-6 col-lg-4">
            <div class="card border-dark">
                <div class="card-header">
                    <h4 class="card-title">
                        <a href="{{ route('firearms.show', $firearm->id) }}">{{ $firearm->label }}</a><br />
                    </h4>
                    {{ $firearm->manufacturer }} {{ $firearm->model }}
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Rounds Fired: <span class="badge badge-dark pull-right">{{ $firearm->totalRoundsFired() }}</span></li>
                    </ul>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('firearms.edit', $firearm->id) }}">Edit</a>
                    <a class="card-link" href="{{ route('firearms.destroy', $firearm->id) }}">Delete</a>
                    <hr />
                    <a class="card-link" href="{{ route('shootsFirearms', $firearm->id) }}">Shoots</a>
                </div>
            </div>
        </div>
        @endforeach
    @endif
    </div>

@endsection
