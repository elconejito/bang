@extends('layouts.master')

@section('title', 'Firearms')

@section('content')
    {!! Breadcrumbs::render('firearms') !!}
    <a href="{{ route('firearms.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Firearm</a>
    <h1>Firearms</h1>
    @if ( $firearms->isEmpty() )
        <p>No Firearms yet.</p>
    @else
        <div class="card-deck">
        @foreach ( $firearms as $firearm )
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><a href="{{ route('firearms.show', $firearm->id) }}">{{ $firearm->label }}</a><br /><small>{{ $firearm->manufacturer }} {{ $firearm->model }}</small></h4>
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
        @endforeach
        </div>
    @endif
@endsection
