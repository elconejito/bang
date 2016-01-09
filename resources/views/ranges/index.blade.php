@extends('layouts.master')

@section('title', 'Ranges')

@section('content')
    <div class="container">

        <h1>Ranges</h1>
        <p><a href="{{ route('ranges.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $ranges->isEmpty() )
            <p>No Ranges yet.</p>
            <p><a href="{{ route('ranges.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $ranges as $range )
                <div class="col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-block">
                            <div class="dropdown pull-right">
                                <button type="button" class="btn btn-info-outline" id="bullet-card-menu-{{ $range->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <a class="dropdown-item" href="{{ route('ranges.edit', $range->id) }}">Edit</a>
                                    <a class="dropdown-item" href="{{ route('ranges.destroy', $range->id) }}">Delete</a>
                                </div>
                            </div>
                            <h4 class="card-title">{{ $range->label }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
