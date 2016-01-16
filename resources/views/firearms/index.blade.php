@extends('layouts.master')

@section('title', 'Firearms')

@section('content')
    <div class="container">

        <h1>Firearms</h1>
        <p><a href="{{ route('firearms.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $firearms->isEmpty() )
            <p>No Firearms yet.</p>
            <p><a href="{{ route('firearms.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $firearms as $firearm )
                <div class="col-sm-6 col-md-4">
                    <div class="card card-primary-outline">
                        <div class="dropdown">
                            <a href="#" id="firearm-card-menu-{{ $firearm->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">
                                <a class="dropdown-item" href="{{ route('firearms.edit', $firearm->id) }}">Edit</a>
                                <a class="dropdown-item" href="{{ route('firearms.destroy', $firearm->id) }}">Delete</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('shoots.index') }}">Shoots</a>
                            </div>
                        </div>
                        <div class="card-block">
                            <h4 class="card-title"><small>{{ $firearm->manufacturer }}</small><br />{{ $firearm->model }}</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Rounds Fired: <span class="label label-default pull-right">{{ $firearm->totalRoundsFired() }}</span></li>
                        </ul>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
