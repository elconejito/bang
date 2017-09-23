
@extends('layouts.master')

@section('title', 'Magazines')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Magazines',
        'breadcrumbName' => 'magazines',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('magazines.create'),
        'buttonRouteParams' => null,
        'buttonText' => 'Add New Magazine'
    ])

    @if ( $magazines->isEmpty() )
        <p>No Magazines yet.</p>
    @else
        <div class="card-deck">
            @foreach ( $magazines as $magazine )
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <small>{{ $magazine->manufacturer }}</small><br />
                            <a href="{{ route('magazines.show', $magazine->id) }}">{{ $magazine->label }}</a>

                        </h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Rounds Fired: <span class="badge badge-dark pull-right">0</span></li>
                        </ul>
                    </div>
                    <div class="card-footer text-muted">
                        <a class="card-link" href="{{ route('magazines.edit', $magazine->id) }}">Edit</a>
                        <a class="card-link" href="{{ route('magazines.destroy', $magazine->id) }}">Delete</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
