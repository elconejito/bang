<?php
use App\Bullet;
use App\Cartridge;
?>

@extends('layouts.master')

@section('title', 'Bullet')

@section('content')
    {!! Breadcrumbs::render('bullets', $cartridge) !!}
    <a href="{{ route('cartridges.bullets.create', $cartridge) }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Bullet</a>
    <h1>Bullets</h1>
    @if ( $bullets->isEmpty() )
        <p>No Bullets yet.</p>
    @else
        @foreach( $cartridges as $cartridge )
        <h2><a href="#">{{ $cartridge->size }}</a></h2>
        <div class="row">
            @foreach( $bullets->where('cartridge_id', $cartridge->id)->sortByDesc($sort) as $bullet )
            <div class="col-sm-6 col-lg-4">
                <div class="card {{ ($bullet->inventory > 0 ? 'card-primary-outline' : 'card-secondary-outline') }}">
                    <div class="dropdown">
                        <a href="#" id="bullet-card-menu-{{ $bullet->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                            <a class="dropdown-item" href="{{ route('cartridges.bullets.edit', $bullet->id) }}">Edit</a>
                            <a class="dropdown-item" href="{{ route('cartridges.bullets.destroy', $bullet->id) }}">Delete</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Orders</a>
                            <a class="dropdown-item" href="#">Shoots</a>
                        </div>
                    </div>
                    <div class="card-block card-flex">
                        <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                        <h4 class="card-title"><small><a href="#">{{ $bullet->manufacturer }}</a></small><br />{{ $bullet->name }}</h4>
                    </div>
                    <div class="card-block">
                        <p class="card-text">
                            <a href="#"><span class="label label-pill label-default">{{ $bullet->cartridge->size }}</span></a>
                            <a href="#"><span class="label label-pill label-default">{{ $bullet->purpose->label }}</span></a>
                            <a href="#"><span class="label label-pill label-default">{{ $bullet->weight }}gr</span></a>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    @endif
@endsection
