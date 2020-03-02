@php
use App\Models\CaliberType;
@endphp

@extends('layouts.master')

@section('title', 'New | Caliber')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Add a new Caliber',
        'breadcrumbName' => 'calibers.create',
        'breadcrumbParams' => null,
        'hasButton' => false
    ])

    <form action="{{ route('calibers.store') }}" method="post" name="caliber-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="caliber" class="col-sm-2 col-form-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label of Caliber" required>
                <small class="form-text text-muted">
                    The full name of the caliber such as 9mm Luger, 7.62x39mm, .308 Winchester, etc
                </small>
            </div>
        </div>

        <div class="form-group row">
            <label for="label" class="col-sm-2 col-form-label">Short Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="short_label" name="short_label" placeholder="Short Label for Caliber" required>
                <small class="form-text text-muted">
                    The label used throughout the app
                </small>
            </div>
        </div>

        <div class="form-group row">
            <label for="caliber_type_id" class="col-sm-2 col-form-label">Caliber Type</label>
            <div class="col-sm-10">
                <select class="form-control" id="caliber_type_id" name="caliber_type_id" required>
                    @foreach( CaliberType::all() as $caliberType )
                    <option value="{{ $caliberType->id }}">{{ $caliberType->label }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">
                    The type of caliber such as rimfire, centerfire, or shotgun
                </small>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary">Add New</button>
            </div>
        </div>
    </form>

@endsection
