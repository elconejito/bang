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

    <div class="row">
        <div class="col">

            <form action="{{ route('calibers.ammunitions.store', $caliber) }}" method="post" name="ammunition-create">
                @csrf

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
                    <label for="label" class="col-sm-2 form-control-label">Label</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="label" name="label" placeholder="Label">
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
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Add New</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection
