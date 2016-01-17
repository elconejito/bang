<?php
use App\Bullet;
use App\Cartridge;
?>

@extends('layouts.master')

@section('title', 'Bullet')

@section('content')
    <div class="container">

        <h1>Bullets</h1>
        <p><a href="{{ route('bullets.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( Bullet::all()->isEmpty() )
            <p>No Bullets yet.</p>
            <p><a href="{{ route('bullets.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            @foreach( Cartridge::all() as $cartridge )
            <h2>{{ $cartridge->size }}</h2>
            <div class="row">
                @foreach ( Bullet::where('inventory', '>', 0)->where('cartridge_id', $cartridge->id)->orderBy('manufacturer', 'asc')->orderBy('model', 'asc')->get() as $bullet )
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-primary-outline">
                        <div class="dropdown">
                            <a href="#" id="bullet-card-menu-{{ $bullet->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                <a class="dropdown-item" href="{{ route('bullets.edit', $bullet->id) }}">Edit</a>
                                <a class="dropdown-item" href="{{ route('bullets.destroy', $bullet->id) }}">Delete</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a>
                                <a class="dropdown-item" href="{{ route('shootsBullets', $bullet->id) }}">Shoots</a>
                            </div>
                        </div>
                        <div class="card-block card-flex">
                            <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                            <h4 class="card-title"><small>{{ $bullet->manufacturer }}</small><br />{{ $bullet->model }}</h4>
                        </div>
                        <div class="card-block">
                            <p class="card-text">
                                <span class="label label-pill label-default">{{ $bullet->cartridge->size }}</span>
                                <span class="label label-pill label-default">{{ $bullet->purpose->label }}</span>
                                <span class="label label-pill label-default">{{ $bullet->weight }}gr</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="row">
                @foreach ( Bullet::where('inventory', '=', 0)->where('cartridge_id', $cartridge->id)->orderBy('manufacturer', 'asc')->orderBy('model', 'asc')->get() as $bullet )
                    <div class="col-sm-6 col-lg-4">
                        <div class="card card-secondary-outline">
                            <div class="dropdown">
                                <a href="#" id="bullet-card-menu-{{ $bullet->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{ route('bullets.edit', $bullet->id) }}">Edit</a>
                                    <a class="dropdown-item" href="{{ route('bullets.destroy', $bullet->id) }}">Delete</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a>
                                    <a class="dropdown-item" href="{{ route('shootsBullets', $bullet->id) }}">Shoots</a>
                                </div>
                            </div>
                            <div class="card-block card-flex">
                                <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                                <h4 class="card-title"><small>{{ $bullet->manufacturer }}</small><br />{{ $bullet->model }}</h4>
                            </div>
                            <div class="card-block">
                                <p class="card-text">
                                    <span class="label label-pill label-default">{{ $bullet->cartridge->size }}</span>
                                    <span class="label label-pill label-default">{{ $bullet->purpose->label }}</span>
                                    <span class="label label-pill label-default">{{ $bullet->weight }}gr</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endforeach
        @endif
    </div><!-- /.container -->
@endsection
