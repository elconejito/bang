@extends('layouts.master')

@section('title', 'New | Inventory')

@section('content')
    {!! Breadcrumbs::render('inventoryCreate', $order) !!}
    <h1>Add Inventory</h1>
    <form action="{{ route('orders.inventories.store', $order) }}" method="post" name="order-create">
        {{ csrf_field() }}
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
            <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
            <div class="col-sm-10">
                <select class="form-control" id="bullet_id" name="bullet_id">
                    {!! \App\Helpers\FormHelper::select(\App\Bullet::all(), 'id', 'getLabel') !!}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="notes" class="col-sm-2 form-control-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Add New</button>
            </div>
        </div>
    </form>
@endsection
