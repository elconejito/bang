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
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-primary-outline">
                        <div class="dropdown">
                            <a href="#" id="cartridge-card-menu-{{ $cartridge->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">
                                <a class="dropdown-item" href="{{ route('cartridges.edit', $cartridge->id) }}">Edit</a>
                                <a class="dropdown-item" href="{{ route('cartridges.destroy', $cartridge->id) }}">Delete</a>
                            </div>
                        </div>
                        <div class="card-block card-flex">
                            <div class="rounds"><span>{{ $cartridge->totalRounds() }}</span>rnds</div>
                            <h4 class="card-title"><a href="{{ route('bulletsCartridges', $cartridge->id) }}">{{ $cartridge->size }}</a></h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            @foreach( \App\Purpose::all() as $purpose )
                                @if ( $purpose->totalRounds($cartridge) )
                                <li class="list-group-item">{{ $purpose->label }}: <span class="label label-default pull-right">{{ $purpose->totalRounds($cartridge) }}</span></li>
                                @endif
                            @endforeach
                        </ul>
                        <ul class="list-group list-group-flush">
                            @foreach( $cartridge->firearms as $firearm )
                                <li class="list-group-item"><a href="{{ route('firearms.show', $firearm->id) }}">{{ $firearm->label }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
