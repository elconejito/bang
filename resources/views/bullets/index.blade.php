@extends('layouts.master')

@section('title', 'Bullet')

@section('content')
    <div class="container">

        <h1>Bullets</h1>
        <p><a href="{{ route('bullets.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $bullets->isEmpty() )
            <p>No Bullets yet.</p>
            <p><a href="{{ route('bullets.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $bullets as $bullet )
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header">{{ $bullet->cartridge->size }}</div>
                        <div class="card-block">
                            <h4 class="card-title"><small>{{ $bullet->manufacturer }}</small><br />{{ $bullet->model }}</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-info">Rounds: {{ $bullet->inventory }}</li>
                            <li class="list-group-item">Weight: {{ $bullet->weight }}</li>
                            <li class="list-group-item">Purpose: {{ $bullet->purpose }}</li>
                        </ul>
                        <div class="card-block">
                            <nav class="nav nav-inline">
                                <a class="nav-link" href="{{ route('bullets.edit', $bullet->id) }}">Edit</a>
                                <a class="nav-link" href="{{ route('bullets.destroy', $bullet->id) }}">Delete</a>
                            </nav>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
