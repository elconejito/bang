@extends('layouts.master')

@section('title', 'New | Target')

@section('content')

    <h1>Create Target</h1>
    <form action="{{ route('targets.store') }}" method="post" name="target-create" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="trip_id" id="trip_id" value="{{ $trip_id }}">
        <input type="hidden" name="shoot_id" id="shoot_id" value="{{ $shoot_id }}">
        <input type="hidden" name="firearm_id" id="firearm_id" value="{{ $firearm_id }}">
        <input type="hidden" name="bullet_id" id="bullet_id" value="{{ $bullet_id }}">

        <div class="form-group row">
            <label for="label" class="col-sm-2 form-control-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label">
            </div>
        </div>
        <div class="form-group row">
            <label for="distance" class="col-sm-2 form-control-label">Distance</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="distance" name="distance" placeholder="Distance">
            </div>
        </div>
        <div class="form-group row">
            <label for="group_size" class="col-sm-2 form-control-label">Group Size</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="group_size" name="group_size" placeholder="Group Size">
            </div>
        </div>

        <div class="form-group row">
            <label for="file" class="col-sm-2 form-control-label">Target Image</label>
            <div class="col-sm-10">
                <input type="file" class="form-control-file" id="file" name="file">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-2 ml-auto">
                <button type="submit" class="btn btn-primary btn-block">Add New</button>
            </div>
        </div>
    </form>

@endsection
