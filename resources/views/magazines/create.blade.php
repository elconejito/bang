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
            <label for="model_name" class="col-sm-2 form-control-label">Model Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="model_name" name="model_name" placeholder="Model Name">
            </div>
        </div>
        <div class="form-group row">
            <label for="capacity" class="col-sm-2 form-control-label">Capacity</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="capacity" name="capacity" placeholder="Capacity">
            </div>
        </div>
        <div class="form-group row">
            <label for="serial_number" class="col-sm-2 form-control-label">Serial Number</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Serial Number">
            </div>
        </div>
        <div class="form-group row">
            <label for="id_marking" class="col-sm-2 form-control-label">ID Marking</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="id_marking" name="id_marking" placeholder="ID Marking" aria-describedby="id_marking_help_block">
                <small id="id_marking_help_block" class="form-text text-muted">
                    A identifying mark or label on the magazine
                </small>
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
            <div class="col-sm-2 ml-auto">
                <button type="submit" class="btn btn-primary btn-block">Add New</button>
            </div>
        </div>
    </form>

@endsection
