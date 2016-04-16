@extends('layouts.master')

@section('title', 'Edit | Range Trip')

@section('content')
    {!! Breadcrumbs::render('trip', $trip) !!}
    <h1>Edit Range Trip</h1>
    <form action="{{ route('trips.update', $trip->id) }}" method="post" name="shoot-edit">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="put" />
        <div class="form-group row">
            <label for="trip_date" class="col-sm-2 form-control-label">Trip Date</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="trip_date" name="trip_date" value="{{ $trip->trip_date->toDateString() }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="range_id" class="col-sm-2 form-control-label">Range</label>
            <div class="col-sm-10">
                <select class="form-control" id="range_id" name="range_id">
                    {!! \App\Helpers\FormHelper::select(\App\Range::all(), 'id', 'label', $trip->range_id) !!}
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection
