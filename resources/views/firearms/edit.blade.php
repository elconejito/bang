@extends('layouts.master')

@section('title', 'Edit | Firearm')

@section('content')
    <div class="container">

        <h1>Edit Firearm</h1>
        <form action="{{ route('firearms.update', $firearm->id) }}" method="post" name="bullet-edit">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            <div class="form-group row">
                <label for="label" class="col-sm-2 form-control-label">Label</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="label" name="label" placeholder="Label" value="{{ $firearm->label }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer" value="{{ $firearm->manufacturer }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="model" class="col-sm-2 form-control-label">Model</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="model" name="model" placeholder="Model" value="{{ $firearm->model }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="cartridge_id" class="col-sm-2 form-control-label">Cartridge</label>
                <div class="col-sm-10">
                    <select class="form-control" id="cartridge_id" name="cartridge_id">
                        {!! \App\Helpers\FormHelper::select(\App\Cartridge::all(), 'id', 'size', $firearm->cartridge_id) !!}
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="notes" class="col-sm-2 form-control-label">Notes</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $firearm->notes }}</textarea>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>

    </div><!-- /.container -->
@endsection