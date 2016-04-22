@extends('layouts.master')

@section('title', 'Ranges')

@section('content')
    <div class="container">
        <a href="{{ route('ranges.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Range</a>
        <h1>Ranges</h1>
        @if ( $ranges->isEmpty() )
            <p>No Ranges yet.</p>
        @else
            <div class="row">
            @foreach ( $ranges as $range )
                <div class="col-sm-6 col-md-4">
                    <div class="card card-primary-outline">
                        <div class="dropdown">
                            <a href="#" id="range-card-menu-{{ $range->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">
                                <a class="dropdown-item" href="{{ route('ranges.edit', $range->id) }}">Edit</a>
                                <a class="dropdown-item" href="{{ route('ranges.destroy', $range->id) }}">Delete</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('shootsRanges', $range->id) }}">Shoots</a>
                            </div>
                        </div>
                        <div class="card-block">
                            <h4 class="card-title">{{ $range->label }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
