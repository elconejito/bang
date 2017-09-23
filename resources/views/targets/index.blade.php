
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

    @if ( $targets->isEmpty() )
        <p>No Targets yet.</p>
    @else
        <div class="card-deck">
            @foreach ( $targets as $target )
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <small>{{ $target->manufacturer }}</small><br />
                            <a href="{{ route('targets.show', $target->id) }}">{{ $target->label }}</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Rounds Fired: <span class="badge badge-dark pull-right">0</span></li>
                        </ul>
                    </div>
                    <div class="card-footer text-muted">
                        <a class="card-link" href="{{ route('targets.edit', $target->id) }}">Edit</a>
                        <a class="card-link" href="{{ route('targets.destroy', $target->id) }}">Delete</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
