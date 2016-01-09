@extends('layouts.master')

@section('title', 'New | Order')

@section('content')
    <div class="container">

        <h1>Create Order</h1>
        <form action="{{ route('orders.store') }}" method="post" name="order-create">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="order_date" class="col-sm-2 form-control-label">Order Date</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="order_date" name="order_date" value="{{ Carbon\Carbon::now()->toDateString() }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="boxes" class="col-sm-2 form-control-label">Boxes</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="boxes" name="boxes">
                </div>
            </div>
            <div class="form-group row">
                <label for="rounds_per_box" class="col-sm-2 form-control-label">Rounds/Box</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="rounds_per_box" name="rounds_per_box">
                </div>
            </div>
            <div class="form-group row">
                <label for="cost_per_box" class="col-sm-2 form-control-label">Cost/Box</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="cost_per_box" name="cost_per_box">
                </div>
            </div>
            <div class="form-group row">
                <label for="store_id" class="col-sm-2 form-control-label">Store</label>
                <div class="col-sm-10">
                    <select class="form-control" id="store_id" name="store_id">
                        {!! \App\Helpers\FormHelper::select(\App\Store::all(), 'id', 'label') !!}
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
                <div class="col-sm-10">
                    <select class="form-control" id="bullet_id" name="bullet_id">
                        {!! \App\Helpers\FormHelper::select(\App\Bullet::all(), 'id', ['manufacturer', 'model', 'weight']) !!}
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Add New</button>
                </div>
            </div>
        </form>
    </div><!-- /.container -->
@endsection
