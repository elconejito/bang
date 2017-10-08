@extends('layouts.master')

@section('title', 'Orders')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Orders',
        'breadcrumbName' => 'orders',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('orders.create'),
        'buttonText' => 'Add New Order'
    ])

    @if ( $orders->isEmpty() )
        <p>No Orders yet.</p>
    @else
        <table class="table table-hover">
            <thead class="thead-default">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th class="text-right">Rounds</th>
                    <th class="text-right">Cost</th>
                    <th>Store</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $orders->sortByDesc('order_date') as $order )
                <tr>
                    <td scope="row">
                        <div class="btn-group">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">View</a>
                            <button type="button" class="btn btn-secondary btn-sm" id="order-menu-{{ $order->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="order-menu-{{ $order->id }}">
                                <a href="{{ route('orders.edit', $order->id) }}" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                <a href="{{ route('orders.destroy', $order->id) }}" class="dropdown-item"><i class="fa fa-trash"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                    <td>{{ $order->order_date->toFormattedDateString() }}</td>
                    <td class="text-right">{{ $order->getRounds() }}</td>
                    <td class="text-right">{{ $order->getTotalCost() }}</td>
                    <td><a href="{{ route('ordersStores', $order->store->id) }}">{{ $order->store->label }}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
