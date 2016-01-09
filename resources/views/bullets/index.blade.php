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
                <div class="col-sm-4 col-lg-3">
                    <div class="card">
                        <div class="card-block">
                            <div class="dropdown pull-right">
                                <button type="button" class="btn btn-info-outline" id="bullet-card-menu-{{ $bullet->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{ route('bullets.edit', $bullet->id) }}">Edit</a>
                                    <a class="dropdown-item" href="{{ route('bullets.destroy', $bullet->id) }}">Delete</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a>
                                    <a class="dropdown-item" href="{{ route('shoots.index') }}">Shoots</a>
                                </div>
                            </div>
                            <h4 class="card-title"><small>{{ $bullet->manufacturer }}</small><br />{{ $bullet->model }}</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Rounds: <span class="label label-default pull-right">{{ $bullet->inventory }}</span></li>
                            <li class="list-group-item">Cartridge: <span class="pull-right">{{ $bullet->cartridge->size }}</span></li>
                            <li class="list-group-item">Weight: <span class="pull-right">{{ $bullet->weight }}gr</span></li>
                            <li class="list-group-item">Purpose: <span class="pull-right">{{ $bullet->purpose->label }}</span></li>
                        </ul>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
