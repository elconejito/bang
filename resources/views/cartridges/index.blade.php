@extends('layouts.master')

@section('title', 'Cartridges')

@section('content')
    <div class="container">

        <h1>Cartridges</h1>
        <p><a href="{{ route('cartridges.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $cartridges->isEmpty() )
            <p>No Cartridges yet.</p>
            <p><a href="{{ route('cartridges.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $cartridges as $cartridge )
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-block">
                            <h4 class="card-title">{{ $cartridge->size }}</h4>
                        </div>
                        <div class="card-block">
                            <nav class="nav nav-inline">
                                <a class="nav-link" href="{{ route('cartridges.edit', $cartridge->id) }}">Edit</a>
                                <a class="nav-link" href="{{ route('cartridges.destroy', $cartridge->id) }}">Delete</a>
                            </nav>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
