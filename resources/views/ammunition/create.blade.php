@php

use App\Models\Caliber;
use App\Models\Purpose;
use App\Helpers\FormHelper;

@endphp

@extends('layouts.master')

@section('title', 'New | Ammunition')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'New Ammunition',
        'breadcrumbName' => 'ammunitions.create',
        'breadcrumbParams' => $caliber,
        'hasButton' => false,
    ])

    <form action="{{ route('calibers.ammunitions.store', $caliber) }}" method="post" name="ammunition-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer">
            </div>
        </div>
        <div class="form-group row">
            <label for="Name" class="col-sm-2 form-control-label">Name</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" placeholder="Name">
            </div>
        </div>
        <div class="form-group row">
            <label for="weight" class="col-sm-2 form-control-label">Weight (gr)</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight">
            </div>
        </div>
        <div class="form-group row">
            <label for="caliber_id" class="col-sm-2 form-control-label">Caliber</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="caliber" name="caliber" value="{{ $caliber->label }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="purpose_id" class="col-sm-2 form-control-label">Purpose</label>
            <div class="col-sm-10">
                <select class="form-control" id="purpose_id" name="purpose_id">
                    {!! FormHelper::select(Purpose::all(), 'id', ['label']) !!}
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
