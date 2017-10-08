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

    @if ( $cartridges->isEmpty() )
        <p>No Cartridges yet.</p>
    @else
        <div class="card-deck">
        @foreach ( $cartridges as $cartridge )
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><a href="{{ route('cartridges.bullets.index', $cartridge->id) }}">{{ $cartridge->label }}</a><br /><small>{{ $cartridge->size }}</small></h4>
                    <div class="rounds"><span>{{ $cartridge->totalRounds() }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Purpose</h5>
                    <ul class="list-group list-group-flush">
                        @foreach( \App\Purpose::all() as $purpose )
                            @if ( $purpose->totalRounds($cartridge) )
                                <li class="list-group-item">{{ $purpose->label }}: <span class="badge badge-dark pull-right">{{ $purpose->totalRounds($cartridge) }}</span></li>
                            @endif
                        @endforeach
                    </ul>
                    <h5>Firearms</h5>
                    <ul class="list-group list-group-flush">
                        @foreach( $cartridge->firearms as $firearm )
                            <li class="list-group-item"><a href="{{ route('firearms.show', $firearm->id) }}">{{ $firearm->label }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('cartridges.edit', $cartridge->id) }}">Edit</a>
                    <a class="card-link" href="{{ route('cartridges.destroy', $cartridge->id) }}">Delete</a>
                </div>
            </div>
        @endforeach
        </div>
    @endif
@endsection
