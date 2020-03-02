@php

    use App\Models\Caliber;
    use App\Models\Purpose;
    use App\Helpers\FormHelper;

@endphp

@extends('layouts.master')

@section('title', 'Edit ' . $caliber->label . ' | Bullet')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => $ammunition->getLabel(),
        'breadcrumbName' => 'ammunition.edit',
        'breadcrumbParams' => $ammunition,
        'hasButton' => false,
    ])

    <div class="row">
        <div class="col">

            <form action="{{ route('calibers.ammunitions.update', [$caliber->id, $ammunition->id]) }}" method="post" name="ammunition-edit">
                @csrf
                @method('PUT')

                <div class="form-group row">
                    <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer" value="{{ $ammunition->manufacturer }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-sm-2 form-control-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ $ammunition->name }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="weight" class="col-sm-2 form-control-label">Weight (gr)</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight" value="{{ $ammunition->weight }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cartridge_id" class="col-sm-2 form-control-label">Caliber</label>
                    <div class="col-sm-10">
                        <input type="text" readonly class="form-control-plaintext" id="caliber" name="caliber" value="{{ $caliber->label }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="purpose_id" class="col-sm-2 form-control-label">Purpose</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="purpose_id" name="purpose_id">
                            {!! FormHelper::select(Purpose::all(), 'id', ['label'], $ammunition->purpose_id) !!}
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Save Edits</button>
                    </div>
                </div>
            </form>

        </div>

    </div>

@endsection
