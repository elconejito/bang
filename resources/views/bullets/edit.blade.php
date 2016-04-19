@extends('layouts.master')

@section('title', 'Edit | Bullet')

@section('content')
    <div class="container">

        <h1>Edit Bullet</h1>
        <form action="{{ route('bullets.update', $bullet->id) }}" method="post" name="bullet-edit">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer" value="{{ $bullet->manufacturer }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="model" class="col-sm-2 form-control-label">Model</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="model" name="model" placeholder="Model" value="{{ $bullet->model }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="weight" class="col-sm-2 form-control-label">Weight (gr)</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" value="{{ $bullet->weight }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="cartridge_id" class="col-sm-2 form-control-label">Cartridge</label>
                <div class="col-sm-10">
                    <select class="form-control" id="cartridge_id" name="cartridge_id">
                        {!! \App\Helpers\FormHelper::select(\App\Cartridge::all(), 'id', ['size'], $bullet->cartridge_id) !!}
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="purpose_id" class="col-sm-2 form-control-label">Purpose</label>
                <div class="col-sm-10">
                    <select class="form-control" id="purpose_id" name="purpose_id">
                        {!! \App\Helpers\FormHelper::select(\App\Purpose::all(), 'id', ['label'], $bullet->purpose_id) !!}
                    </select>
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
