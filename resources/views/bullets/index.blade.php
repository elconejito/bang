<?php
use App\Bullet;
use App\Cartridge;
?>

@extends('layouts.master')

@section('title', 'Bullet')

@section('content')
    {!! Breadcrumbs::render('bullets', $cartridge) !!}
    <a href="{{ route('cartridges.bullets.create', $cartridge) }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Bullet</a>
    <h1>{{ $cartridge->size }} Bullets</h1>
    @if ( $bullets->isEmpty() )
        <p>No Bullets yet.</p>
    @else
        <div class="card-deck">
            @foreach( $bullets as $bullet )
            <div class="card border-primary">
                <div class="card-header">
                    <h4 class="card-title"><small>{{ $bullet->manufacturer }}</small><br /><a href="{{ route('cartridges.bullets.show', [$cartridge->id, $bullet->id]) }}">{{ $bullet->name }}</a></h4>
                    <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <a href="#"><span class="badge badge-dark">{{ $bullet->cartridge->size }}</span></a>
                        <a href="#"><span class="badge badge-dark">{{ $bullet->purpose->label }}</span></a>
                        <a href="#"><span class="badge badge-dark">{{ $bullet->weight }}gr</span></a>
                    </p>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('cartridges.bullets.edit', [$cartridge->id, $bullet->id]) }}">Edit</a>
                    <a class="card-link" href="{{ route('cartridges.bullets.destroy', [$cartridge->id, $bullet->id]) }}">Delete</a>
                    <hr />
                    <span class="card-link">Orders</span>
                    <span class="card-link">Shoots</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="card-deck">
            @foreach( $bullets_inactive as $bullet )
            <div class="card border-secondary">
                <div class="card-header">
                    <h4 class="card-title"><small>{{ $bullet->manufacturer }}</small><br /><a href="{{ route('cartridges.bullets.show', [$cartridge->id, $bullet->id]) }}">{{ $bullet->name }}</a></h4>
                    <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <a href="#"><span class="badge badge-dark">{{ $bullet->cartridge->size }}</span></a>
                        <a href="#"><span class="badge badge-dark">{{ $bullet->purpose->label }}</span></a>
                        <a href="#"><span class="badge badge-dark">{{ $bullet->weight }}gr</span></a>
                    </p>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('cartridges.bullets.edit', [$cartridge->id, $bullet->id]) }}">Edit</a>
                    <a class="card-link" href="{{ route('cartridges.bullets.destroy', [$cartridge->id, $bullet->id]) }}">Delete</a>
                    <hr />
                    <span class="card-link">Orders</span>
                    <span class="card-link">Shoots</span>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@endsection
