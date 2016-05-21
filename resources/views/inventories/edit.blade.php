@extends('layouts.master')

@section('title', 'Edit | Inventory')

@section('content')
    {!! Breadcrumbs::render('inventoryEdit', $inventory) !!}
    <h1>Edit Inventory</h1>
    <form action="{{ route('orders.inventories.update', [$inventory->order->id, $inventory->id]) }}" method="post" name="inventory-edit">
        <input type="hidden" name="_method" value="put" />
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="boxes" class="col-sm-2 form-control-label">Boxes</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="boxes" name="boxes" value="{{ $inventory->boxes }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="rounds_per_box" class="col-sm-2 form-control-label">Rounds/Box</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="rounds_per_box" name="rounds_per_box" value="{{ $inventory->rounds_per_box }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="cost_per_box" class="col-sm-2 form-control-label">Cost/Box</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="cost_per_box" name="cost_per_box" value="{{ $inventory->cost_per_box }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
            <div class="col-sm-10">
                <select class="form-control" id="bullet_id" name="bullet_id">
                    {!! \App\Helpers\FormHelper::select(\App\Bullet::all(), 'id', 'getLabel', $inventory->bullet_id) !!}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="notes" class="col-sm-2 form-control-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $inventory->notes }}</textarea>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection
