@extends('layouts.master')

@section('title', 'New | Bullet')

@section('content')
    <div class="container">

        <h1>Create Bullet</h1>
        <form action="{{ route('bullets.store') }}" method="post" name="bullet-create">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer">
                </div>
            </div>
            <div class="form-group row">
                <label for="model" class="col-sm-2 form-control-label">Model</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="model" name="model" placeholder="Model">
                </div>
            </div>
            <div class="form-group row">
                <label for="weight" class="col-sm-2 form-control-label">Weight (gr)</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight">
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
                <label for="purpose_id" class="col-sm-2 form-control-label">Purpose</label>
                <div class="col-sm-10">
                    <select class="form-control" id="purpose_id" name="purpose_id">
                        {!! \App\Helpers\FormHelper::select(\App\Purpose::all(), 'id', ['label']) !!}
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
