@extends('layouts.master')

@section('title', 'Stores')

@section('content')
    {!! Breadcrumbs::render('stores') !!}
    <a href="{{ route('stores.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New Store</a>
    <h1>Stores</h1>
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
