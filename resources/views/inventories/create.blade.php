@extends('layouts.master')

@section('title', 'New | Inventory')

@section('content')
    @include('layouts.partials.page-header', [
        'pageTitle' => 'Add Inventory',
        'breadcrumbName' => 'inventories.create',
        'breadcrumbParams' => $order,
        'hasButton' => false,
    ])

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
                    {!! \App\Helpers\FormHelper::select(\App\Bullet::orderBy('manufacturer', 'asc')->orderBy('name', 'asc')->get(), 'id', 'getLabel') !!}
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
