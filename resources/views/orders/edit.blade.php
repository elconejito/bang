@extends('layouts.master')

@section('title', 'Edit | Order')

@section('content')
    {!! Breadcrumbs::render('orderEdit', $order) !!}
    <h1>Edit Order</h1>
    <form action="{{ route('orders.update', $order->id) }}" method="post" name="order-edit">
        <input type="hidden" name="_method" value="put" />
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="order_date" class="col-sm-2 form-control-label">Order Date</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="order_date" name="order_date" value="{{ $order->order_date->toDateString() }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="store_id" class="col-sm-2 form-control-label">Store</label>
            <div class="col-sm-10">
                <select class="form-control" id="store_id" name="store_id">
                    {!! \App\Helpers\FormHelper::select(\App\Store::all(), 'id', ['label'], $order->store_id) !!}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="notes" class="col-sm-2 form-control-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $order->notes }}</textarea>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection
