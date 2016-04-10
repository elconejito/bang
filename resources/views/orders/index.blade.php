@extends('layouts.master')

@section('title', 'Orders')

@section('content')
    <div class="container">
        <a href="{{ route('orders.create') }}" class="btn btn-success-outline pull-right"><i class="fa fa-plus"></i> Add New</a>
        <h1>Orders</h1>
        @if ( $orders->isEmpty() )
            <p>No Orders yet.</p>
        @else
            <table class="table table-hover">
                <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Rounds</th>
                        <th>Cost</th>
                        <th>Cost/Round</th>
                        <th>Boxes</th>
                        <th>Bullet</th>
                        <th>Store</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $orders as $order )
                    <tr>
                        <td scope="row">{{ $order->id }}</td>
                        <td>{{ $order->order_date->toDateString() }}</td>
                        <td>{{ $order->rounds }}</td>
                        <td>{{ $order->getCost() }}</td>
                        <td>{{ $order->getCostPerRound() }}</td>
                        <td>{{ $order->boxes }} (${{ $order->cost_per_box }} / {{ $order->rounds_per_box }}rnds)</td>
                        <td><a href="{{ route('ordersBullets', $order->bullet->id) }}">{{ $order->bullet->manufacturer }} {{ $order->bullet->model }}</a></td>
                        <td><a href="{{ route('ordersStores', $order->store->id) }}">{{ $order->store->label }}</a></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i></a>
                                <a href="{{ route('orders.destroy', $order->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div><!-- /.container -->
@endsection
