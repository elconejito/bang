@php
use App\Bullet;
use App\Cartridge;
@endphp

@extends('layouts.master')

@section('title', 'Bullet')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => $cartridge->size.' Bullets',
        'breadcrumbName' => 'bullets',
        'breadcrumbParams' => $cartridge,
        'hasButton' => true,
        'buttonLink' => route('cartridges.bullets.create', $cartridge),
        'buttonRouteParams' => $cartridge,
        'buttonText' => 'Add New Bullet'
    ])

    <div class="row">
    @if ( $bullets->isEmpty() )
        <div class="col">
            <p>No Bullets yet.</p>
        </div>
    @else
        @foreach( $bullets as $bullet )
        <div class="col-sm-6 col-lg-4">
            <div class="card border-dark">
                <div class="card-header">
                    <h4 class="card-title">
                        <small>{{ $bullet->manufacturer }}</small><br />
                        <a href="{{ route('cartridges.bullets.show', [$cartridge->id, $bullet->id]) }}">{{ $bullet->name }}</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
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
        </div>
        @endforeach
    @endif
    </div>


    @if($bullets_inactive)
        <h3 class="text-muted">Inactive</h3>
        <div class="row">
            @foreach( $bullets_inactive as $bullet )
            <div class="col-sm-6 col-lg-4">
                <div class="card border-light inactive">
                    <div class="card-header">
                        <h4 class="card-title">
                            <small>{{ $bullet->manufacturer }}</small><br />
                            <a href="{{ route('cartridges.bullets.show', [$cartridge->id, $bullet->id]) }}">{{ $bullet->name }}</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
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
            </div>
            @endforeach
        </div>
    @endif
@endsection
