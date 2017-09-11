
@extends('layouts.master')

@section('title', 'Magazines')

@section('content')
    {!! Breadcrumbs::render('magazines') !!}
    <a href="{{ route('magazines.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Magazine</a>
    <h1>Magazines</h1>
    @if ( $magazines->isEmpty() )
        <p>No Magazines yet.</p>
    @else
        <div class="card-deck">
            @foreach ( $magazines as $magazine )
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><a href="{{ route('magazines.show', $magazine->id) }}">{{ $magazine->label }}</a><br /><small>{{ $magazine->manufacturer }} {{ $magazine->name }}</small></h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Rounds Fired: <span class="badge badge-dark pull-right">0</span></li>
                        </ul>
                    </div>
                    <div class="card-footer text-muted">
                        <a class="card-link" href="{{ route('magazines.edit', $firearm->id) }}">Edit</a>
                        <a class="card-link" href="{{ route('magazines.destroy', $firearm->id) }}">Delete</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
