@extends('layouts.master')

@section('title', 'Ranges')

@section('content')
    {!! Breadcrumbs::render('ranges') !!}
    <a href="{{ route('ranges.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Range</a>
    <h1>Ranges</h1>
    @if ( $ranges->isEmpty() )
        <p>No Ranges yet.</p>
    @else
        <div class="card-deck">
        @foreach ( $ranges as $range )
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $range->label }}</h4>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('ranges.edit', $range->id) }}">Edit</a>
                    <a class="card-link" href="{{ route('ranges.destroy', $range->id) }}">Delete</a>
                    <hr />
                    <a class="card-link" href="{{ route('shootsRanges', $range->id) }}">Shoots</a>
                </div>
            </div>
        @endforeach
        </div>
    @endif
@endsection
