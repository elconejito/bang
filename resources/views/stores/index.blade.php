@extends('layouts.master')

@section('title', 'Stores')

@section('content')
    <div class="container">

        <h1>Stores</h1>
        <p><a href="{{ route('stores.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $stores->isEmpty() )
            <p>No Stores yet.</p>
            <p><a href="{{ route('stores.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $stores as $store )
                <div class="col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-block">
                            <div class="dropdown pull-right">
                                <button type="button" class="btn btn-info-outline" id="bullet-card-menu-{{ $store->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{ route('stores.edit', $store->id) }}">Edit</a>
                                    <a class="dropdown-item" href="{{ route('stores.destroy', $store->id) }}">Delete</a>
                                </div>
                            </div>
                            <h4 class="card-title">{{ $store->label }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
