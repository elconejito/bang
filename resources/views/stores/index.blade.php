@extends('layouts.master')

@section('title', 'Stores')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Stores',
        'breadcrumbName' => 'stores',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('stores.create'),
        'buttonText' => 'Add New Store'
    ])

    @if ( $stores->isEmpty() )
        <p>No Stores yet.</p>
    @else
        <div class="card-deck">
        @foreach ( $stores as $store )
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $store->label }}</h4>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('stores.edit', $store->id) }}">Edit</a>
                    <a class="card-link" href="{{ route('stores.destroy', $store->id) }}">Delete</a>
                    <hr />
                    <a class="card-link" href="{{ route('ordersStores', $store->id) }}">Orders</a>
                </div>
            </div>
        @endforeach
        </div>
    @endif
@endsection
