@extends('layouts.master')

@section('title', 'Orders')

@section('content')
    <div class="container">

        <h1>Orders</h1>
        <p><a href="{{ route('orders.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $orders->isEmpty() )
            <p>No Orders yet.</p>
            <p><a href="{{ route('orders.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <table class="table table-hover">
                <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Rounds</th>
                        <th>Cost</th>
                        <th>Cost/Round</th>
                        <th>Boxes</th>
                        <th>Store</th>
                        <th>Bullet</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $orders as $order )
                    <tr>
                        <td scope="row">{{ $order->id }}</td>
                        <td>{{ $order->rounds }}</td>
                        <td>{{ $order->getCost() }}</td>
                        <td>{{ $order->getCostPerRound() }}</td>
                        <td>{{ $order->boxes }} (${{ $order->cost_per_box }} / {{ $order->rounds_per_box }}rnds)</td>
                        <td>{{ $order->store }}</td>
                        <td>{{ $order->bullet->manufacturer }} {{ $order->bullet->model }}</td>
                        <td>
                            <div class="btn-group">
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
