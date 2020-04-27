@php
use App\Models\CaliberType;
@endphp

@extends('layouts.master')

@section('title', 'Edit | Caliber')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => '<small>Edit Caliber</small><br />'. $caliber->caliber,
        'breadcrumbName' => 'calibers.edit',
        'breadcrumbParams' => $caliber,
        'hasButton' => false
    ])

    <form action="{{ route('calibers.update', $caliber->id) }}" method="post" name="caliber-edit">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="put" />

        <div class="form-group row">
            <label for="caliber" class="col-sm-2 col-form-label">Caliber</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="caliber" name="caliber" placeholder="Caliber of Cartridge" value="{{ $caliber->caliber }}">
                <small class="form-text text-muted">
                    The caliber of the cartridge such as 9mm, 7.62x39mm, .308, or .30-06, etc
                </small>
            </div>
        </div>

        <div class="form-group row">
            <label for="caliber_type_id" class="col-sm-2 col-form-label">Caliber Type</label>
            <div class="col-sm-10">
                <select class="form-control" id="caliber_type_id" name="caliber_type_id" required>
                    @foreach( CaliberType::all() as $caliberType )
                        <option value="{{ $caliberType->id }}" {{ ( $caliberType->id == $caliber->caliber_type_id) ? 'selected' : '' }}>{{ $caliberType->label }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    The type of caliber such as rimfire, centerfire, or shotgun
                </small>
            </div>
        </div>

        <div class="form-group row">
            <label for="label" class="col-sm-2 col-form-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label for Caliber" value="{{ $caliber->label }}">
                <small class="form-text text-muted">
                    The label used throughout the app
                </small>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>

@endsection
