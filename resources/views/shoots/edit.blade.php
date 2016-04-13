@extends('layouts.master')

@section('title', 'Edit | Shoot')

@section('content')
    <div class="container">

        <h1>Edit Shoot</h1>
        <form action="{{ route('trips.shoots.update', $shoot->id) }}" method="post" name="shoot-edit">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            <div class="form-group row">
                <label for="shoot_date" class="col-sm-2 form-control-label">Shoot Date</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="shoot_date" name="shoot_date" value="{{ $shoot->shoot_date->toDateString() }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="rounds" class="col-sm-2 form-control-label">Rounds</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="rounds" name="rounds" value="{{ $shoot->rounds }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="range_id" class="col-sm-2 form-control-label">Range</label>
                <div class="col-sm-10">
                    <select class="form-control" id="range_id" name="range_id">
                        {!! \App\Helpers\FormHelper::select(\App\Range::all(), 'id', 'label', $shoot->range_id) !!}
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="firearm_id" class="col-sm-2 form-control-label">Firearm</label>
                <div class="col-sm-10">
                    <select class="form-control" id="firearm_id" name="firearm_id">
                        {!! \App\Helpers\FormHelper::select(\App\Firearm::all(), 'id', ['manufacturer', 'model'], $shoot->firearm_id) !!}
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
                <div class="col-sm-10">
                    <select class="form-control" id="bullet_id" name="bullet_id">
                        {!! \App\Helpers\FormHelper::select(\App\Bullet::all(), 'id', ['manufacturer', 'model', 'weight'], $shoot->bullet_id) !!}
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
