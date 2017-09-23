@extends('layouts.master')

@section('title', 'Ranges')

@section('content')
    @include('layouts.partials.page-header', [
        'pageTitle' => 'Ranges',
        'breadcrumbName' => 'ranges',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('ranges.create'),
        'buttonText' => 'Add New Range'
    ])

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
                    <span class="card-link">Shoots</span>
                </div>
            </div>
        @endforeach
        </div>
    @endif
@endsection
