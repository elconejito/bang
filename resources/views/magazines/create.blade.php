@extends('layouts.master')

@section('title', 'New | Magazine')

@section('content')

    <h1>Create Magazine</h1>
    <form action="{{ route('magazines.store') }}" method="post" name="magazine-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="label" class="col-sm-2 form-control-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label">
            </div>
        </div>
        <div class="form-group row">
            <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer">
            </div>
        </div>
        <div class="form-group row">
            <label for="name" class="col-sm-2 form-control-label">Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" placeholder="Name">
            </div>
        </div>
        <div class="form-group row">
            <label for="cartridge_id" class="col-sm-2 form-control-label">Cartridge</label>
            <div class="col-sm-10">
                <select class="form-control" id="cartridge_id" name="cartridge_id">
                    {!! \App\Helpers\FormHelper::select(\App\Cartridge::all(), 'id', ['size']) !!}
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
