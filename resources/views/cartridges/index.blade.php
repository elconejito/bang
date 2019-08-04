@php
use App\Models\Purpose;
@endphp

@extends('layouts.master')

@section('title', 'Cartridges')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Cartridges',
        'breadcrumbName' => 'cartridges',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('cartridges.create'),
        'buttonText' => 'Add New Cartridge'
    ])

    <div class="row">
    @if ( $cartridges->isEmpty() )
        <div class="col">
            <p>No Cartridges yet.</p>
        </div>
    @else
        @foreach ( $cartridges as $cartridge )
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <a href="{{ route('cartridges.bullets.index', $cartridge->id) }}">{{ $cartridge->label }}</a>
                    </h4>
                    Size: <code>{{ $cartridge->size }}</code>
                </div>
                <div class="card-body">
                    <div class="rounds">
                        <span>{{ $cartridge->totalRounds() }}</span>total rnds
                    </div>
                    <h5>Firearms</h5>
                    @if ( !$cartridge->firearms->isEmpty() )
                        <p>Used by:
                        @foreach( $cartridge->firearms as $firearm )
                            <a href="{{ route('firearms.show', $firearm->id) }}" class="badge badge-primary">{{ $firearm->label }}</a>
                        @endforeach
                        </p>
                    @else
                        <p>None using this Cartridge.</p>
                    @endif

                    <h5>Purpose</h5>
                    <ul class="list-group list-group-flush">
                        @foreach( Purpose::all() as $purpose )
                            <li class="list-group-item">
                                {{ $purpose->label }}: <span class="badge badge-dark pull-right">{{ $cartridge->bulletsForPurpose($purpose)->sum('inventory') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <a class="card-link" href="{{ route('cartridges.edit', $cartridge->id) }}">Edit</a>
                    <a class="card-link" href="{{ route('cartridges.destroy', $cartridge->id) }}">Delete</a>
                </div>
            </div>
        </div>
        @endforeach
    @endif
    </div>

@endsection
