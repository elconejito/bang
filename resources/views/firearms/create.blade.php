@php
use App\Models\Cartridge;
@endphp

@extends('layouts.master')

@section('title', 'New | Firearm')

@section('content')
    @include('layouts.partials.page-header', [
        'pageTitle' => 'Add a new Firearm',
        'breadcrumbName' => 'firearms.create',
        'breadcrumbParams' => null,
        'hasButton' => false,
    ])

    <div class="row">
        <div class="col">

            <form action="{{ route('firearms.store') }}" method="post" name="label-create">
                @csrf

                <div class="form-group row">
                    <label for="manufacturer" class="col-sm-2 form-control-label">Manufacturer</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer">
                        <small class="form-text text-muted">
                            The manufacturer of this firearm.
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="model" class="col-sm-2 form-control-label">Model</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="model" name="model" placeholder="Model">
                        <small class="form-text text-muted">
                            The model of this firearm.
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="label" class="col-sm-2 form-control-label">Label</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="label" name="label" placeholder="Label">
                        <small class="form-text text-muted">
                            The label for this firearm. This is how it will be referenced throughout the app.
                        </small>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="cartridge_id" class="col-sm-2 form-control-label">Caliber(s)</label>
                    <div class="col-sm-10">
                        <small class="form-text text-muted">
                            Check all the calibers that this firearm is capable of firing
                        </small>
                        <div class="row">
                            @foreach($calibers->chunk(3) as $chunk)
                                <div class="col-sm-4">
                                    @foreach($chunk as $caliber)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $caliber->id }}" id="caliber_id_{{ $caliber->id }}" name="caliber_id">
                                            <label class="form-check-label" for="caliber_id_{{ $caliber->id }}">
                                                {{ $caliber->label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">Add Firearm</button>
                    </div>
                </div>
            </form>

        </div>
    </div><!-- /.row -->

@endsection
