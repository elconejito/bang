@php
use App\Helpers\FormHelper;
use App\Models\Range;
use Carbon\Carbon;
@endphp

@extends('layouts.master')

@section('title', 'New | Training')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'New Range Trip',
        'breadcrumbName' => 'trips.create',
        'breadcrumbParams' => null,
        'hasButton' => false,
    ])

    <form action="{{ route('trips.store') }}" method="post" name="shoot-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="trip_date" class="col-sm-2 form-control-label">Trip Date</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="trip_date" name="trip_date" value="{{ Carbon::now()->toDateString() }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="range_id" class="col-sm-2 form-control-label">Range</label>
            <div class="col-sm-10">
                <select class="form-control" id="range_id" name="range_id">
                    {!! FormHelper::select(Range::all(), 'id', ['label']) !!}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="notes" class="col-sm-2 form-control-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Add New</button>
            </div>
        </div>
    </form>
@endsection
