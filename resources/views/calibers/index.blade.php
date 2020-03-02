@php
use App\Models\Purpose;
@endphp

@extends('layouts.master')

@section('title', 'Calibers')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Calibers',
        'breadcrumbName' => 'calibers',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('calibers.create'),
        'buttonText' => 'Add New Caliber'
    ])

    <div class="row">
    @if ( $calibers->isEmpty() )
        <div class="col">
            <p>No Calibers yet.</p>
        </div>
    @else
        @foreach ( $calibers as $caliber )
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <a href="{{ route('calibers.ammunitions.index', $caliber->id) }}">{{ $caliber->short_label }}</a><br />
                        <small><code>{{ $caliber->caliberType->label }}</code></small>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="rounds">
                        <span>{{ $caliber->totalRounds() }}</span>total rnds
                    </div>
                    <h5>Firearms</h5>
                    @if ( !$caliber->firearms->isEmpty() )
                        <p>Used by:
                        @foreach( $caliber->firearms as $firearm )
                            <a href="{{ route('firearms.show', $firearm->id) }}" class="badge badge-primary">{{ $firearm->label }}</a>
                        @endforeach
                        </p>
                    @else
                        <p>None using this Caliber.</p>
                    @endif

                    <h5>Purpose</h5>
                    <ul class="list-group list-group-flush">
                        @foreach( Purpose::all() as $purpose )
                            <li class="list-group-item">
                                {{ $purpose->label }}: <span class="badge badge-dark pull-right">{{ $caliber->ammunitionForPurpose($purpose)->sum('inventory') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <a class="card-link" href="{{ route('calibers.edit', $caliber->id) }}">Edit</a>
                    <a class="card-link" href="{{ route('calibers.destroy', $caliber->id) }}">Delete</a>
                </div>
            </div>
        </div>
        @endforeach
    @endif
    </div>

@endsection
