@extends('layouts.master')

@section('title', 'Stores')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Stores',
        'breadcrumbName' => 'stores',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('stores.create'),
        'buttonText' => 'Add New Store',
        'buttonTextIcon' => 'fas fa-plus'
    ])

    <div class="row">
    @if ( $stores->isEmpty() )
        <div class="col">
            <p>No Stores yet.</p>
        </div>
    @else
        @foreach ( $stores as $store )
        <div class="col-sm-6 col-lg-4">
            <div class="card border-dark">
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
        </div>
        @endforeach
    @endif
    </div>
@endsection
