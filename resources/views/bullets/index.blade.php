<?php
use App\Bullet;
use App\Cartridge;
?>

@extends('layouts.master')

@section('title', 'Bullet')

@section('content')
    {!! Breadcrumbs::render('bullets') !!}
    <a href="{{ route('bullets.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Bullet</a>
    <h1>Bullets</h1>
    @if ( $bullets->isEmpty() )
        <p>No Bullets yet.</p>
    @else
        @foreach( $cartridges as $cartridge )
        <h2><a href="{{ route('bulletsCartridges', $cartridge->id) }}">{{ $cartridge->size }}</a></h2>
        <div class="row">
            @foreach( $bullets->where('cartridge_id', $cartridge->id)->sortByDesc($sort) as $bullet )
            <div class="col-sm-6 col-lg-4">
                <div class="card {{ ($bullet->inventory > 0 ? 'card-primary-outline' : 'card-secondary-outline') }}">
                    <div class="dropdown">
                        <a href="#" id="bullet-card-menu-{{ $bullet->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                            <a class="dropdown-item" href="{{ route('bullets.edit', $bullet->id) }}">Edit</a>
                            <a class="dropdown-item" href="{{ route('bullets.destroy', $bullet->id) }}">Delete</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('ordersBullets', $bullet->id) }}">Orders</a>
                            <a class="dropdown-item" href="{{ route('shootsBullets', $bullet->id) }}">Shoots</a>
                        </div>
                    </div>
                    <div class="card-block card-flex">
                        <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                        <h4 class="card-title"><small><a href="{{ route('bulletsManufacturers', $bullet->manufacturer) }}">{{ $bullet->manufacturer }}</a></small><br />{{ $bullet->model }}</h4>
                    </div>
                    <div class="card-block">
                        <p class="card-text">
                            <a href="{{ route('bulletsCartridges', $bullet->cartridge->id) }}"><span class="label label-pill label-default">{{ $bullet->cartridge->size }}</span></a>
                            <a href="{{ route('bulletsPurposes', $bullet->purpose->id) }}"><span class="label label-pill label-default">{{ $bullet->purpose->label }}</span></a>
                            <a href="{{ route('bulletsWeights', $bullet->weight) }}"><span class="label label-pill label-default">{{ $bullet->weight }}gr</span></a>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    @endif
@endsection
