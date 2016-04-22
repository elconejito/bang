@extends('layouts.master')

@section('title', 'Firearms')

@section('content')
    <div class="container">
        <a href="{{ route('firearms.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Firearm</a>
        <h1>Firearms</h1>
        @if ( $firearms->isEmpty() )
            <p>No Firearms yet.</p>
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
                                <a class="dropdown-item" href="{{ route('shootsFirearms', $firearm->id) }}">Shoots</a>
                            </div>
                        </div>
                        <div class="card-block">
                            <h4 class="card-title">{{ $firearm->label }}<br /><small>{{ $firearm->manufacturer }} {{ $firearm->model }}</small></h4>
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
