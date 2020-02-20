@extends('layouts.master')

@section('title', 'New | Order')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'New Order',
        'breadcrumbName' => 'orders.create',
        'breadcrumbParams' => null,
        'hasButton' => false,
    ])

    <form action="{{ route('orders.store') }}" method="post" name="order-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="order_date" class="col-sm-2 form-control-label">Order Date</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="order_date" name="order_date" value="{{ Carbon\Carbon::now()->toDateString() }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="store_id" class="col-sm-2 form-control-label">Store</label>
            <div class="col-sm-10">
                <select class="form-control" id="store_id" name="store_id">
                    {!! \App\Helpers\FormHelper::select(\App\Store::all(), 'id', ['label']) !!}
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Add New</button>
            </div>
        </div>
    </form>
@endsection
