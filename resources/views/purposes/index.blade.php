@extends('layouts.master')

@section('title', 'Purposes')

@section('content')
    <div class="container">
        <a href="{{ route('purposes.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New</a>
        <h1>Purposes</h1>
        @if ( $purposes->isEmpty() )
            <p>No Purposes yet.</p>
        @else
            <div class="row">
            @foreach ( $purposes as $purpose )
                <div class="col-sm-6 col-md-4">
                    <div class="card card-primary-outline">
                        <div class="dropdown">
                            <a href="#" id="purpose-card-menu-{{ $purpose->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">
                                <a class="dropdown-item" href="{{ route('purposes.edit', $purpose->id) }}">Edit</a>
                                <a class="dropdown-item" href="{{ route('purposes.destroy', $purpose->id) }}">Delete</a>
                            </div>
                        </div>
                        <div class="card-block card-flex">
                            <div class="rounds"><span>{{ $purpose->totalRounds() }}</span>rnds</div>
                            <h4 class="card-title"><a href="{{ route('bulletsPurposes', $purpose->id) }}">{{ $purpose->label }}</a></h4>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
