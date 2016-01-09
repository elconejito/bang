@extends('layouts.master')

@section('title', 'Purposes')

@section('content')
    <div class="container">

        <h1>Purposes</h1>
        <p><a href="{{ route('purposes.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $purposes->isEmpty() )
            <p>No Purposes yet.</p>
            <p><a href="{{ route('purposes.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $purposes as $purpose )
                <div class="col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-block">
                            <div class="dropdown pull-right">
                                <button type="button" class="btn btn-info-outline" id="bullet-card-menu-{{ $purpose->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{ route('purposes.edit', $purpose->id) }}">Edit</a>
                                    <a class="dropdown-item" href="{{ route('purposes.destroy', $purpose->id) }}">Delete</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('shoots.index') }}">Shoots</a>
                                </div>
                            </div>
                            <h4 class="card-title">{{ $purpose->label }}</h4>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Bullets: <span class="label label-default pull-right">0</span></li>
                            <li class="list-group-item">Rounds: <span class="label label-default pull-right">0</span></li>
                        </ul>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
