@php

use App\Models\Caliber;
use App\Models\CaliberType;
use App\Models\Purpose;
use App\Models\Reference\AmmunitionCasing;
use App\Models\Reference\AmmunitionCondition;
use App\Models\Reference\BulletType;
use App\Models\Reference\PrimerType;
use App\Models\Reference\ShellLength;
use App\Models\Reference\ShellType;
use App\Models\Reference\ShotMaterial;
use App\Helpers\FormHelper;

@endphp

@extends('layouts.master')

@section('title', 'New | Ammunition')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Add new Ammunition',
        'breadcrumbName' => 'ammunitions.create',
        'breadcrumbParams' => $caliber,
        'hasButton' => false,
    ])

    <div class="row">
        <div class="col">

            <form action="{{ route('calibers.ammunitions.store', $caliber) }}" method="post" name="ammunition-create">
                @csrf

                <div class="form-group row">
                    <div class="col-sm-4">
                        <label for="manufacturer" class="form-control-label">Manufacturer</label>
                        <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Manufacturer" required>
                        <small class="form-text text-muted">
                            The name of the manufacturer of the ammunition, like &quot;Federal&quot; or &quot;Hornady&quot;
                        </small>
                    </div>
                    <div class="col-sm-5">
                        <label for="name" class="form-control-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                        <small class="form-text text-muted">
                            How this should show up across the website
                        </small>
                    </div>
                    <div class="col-sm-3">
                        <label for="purpose_id" class="form-control-label">Purpose</label>
                        <select class="form-control" id="purpose_id" name="purpose_id">
                            {!! FormHelper::select(Purpose::all(), 'id', ['label']) !!}
                        </select>
                        <small class="form-text text-muted">
                            Purpose description
                        </small>
                    </div>
                </div>

                @if($caliber->caliberType->id === CaliberType::SHOTGUN)
                <fieldset>
                    <!-- Shotgun Options -->
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="shell_type_id" class="form-control-label">Shell Type</label>
                            <select class="form-control" id="shell_type_id" name="shell_type_id">
                                <option>- Select One -</option>
                                {!! FormHelper::select(ShellType::all(), 'id', ['label']) !!}
                            </select>
                            <small class="form-text text-muted">
                                Shell Type description
                            </small>
                        </div>
                        <div class="col-6">
                            <label for="weight" class="form-control-label">Weight (oz)</label>
                            <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight">
                            <small class="form-text text-muted">
                                Weight description
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-6">
                            <label for="shell_length_id" class="form-control-label">Shell Length</label>
                            <select class="form-control" id="shell_length_id" name="shell_length_id">
                                <option>- Select One -</option>
                                {!! FormHelper::select(ShellLength::all(), 'id', ['label']) !!}
                            </select>
                            <small class="form-text text-muted">
                                Shell Length description
                            </small>
                        </div>

                        <div class="col-6">
                            <label for="shot_material_id" class="form-control-label">Shot Material</label>
                            <select class="form-control" id="shot_material_id" name="shot_material_id">
                                <option>- Select One -</option>
                                {!! FormHelper::select(ShotMaterial::all(), 'id', ['label']) !!}
                            </select>
                            <small class="form-text text-muted">
                                Shot Material description
                            </small>
                        </div>
                    </div>
                </fieldset>

                @else
                <fieldset>
                    <!-- Bullet Options -->
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label for="weight" class="form-control-label">Weight (gr)</label>
                            <input type="text" class="form-control" id="weight" name="weight" placeholder="Weight">
                            <small class="form-text text-muted">
                                Weight description
                            </small>
                        </div>
                        <div class="col-sm-3">
                            <label for="bullet_type_id" class="form-control-label">Bullet Type</label>
                            <select class="form-control" id="bullet_type_id" name="bullet_type_id">
                                <option>- Select One -</option>
                                {!! FormHelper::select(BulletType::all(), 'id', ['label']) !!}
                            </select>
                            <small class="form-text text-muted">
                                Bullet Type description
                            </small>
                        </div>
                        <div class="col-sm-3">
                            <label for="ammunition_casing_id" class="form-control-label">Case Material</label>
                            <select class="form-control" id="ammunition_casing_id" name="ammunition_casing_id">
                                <option>- Select One -</option>
                                {!! FormHelper::select(AmmunitionCasing::all(), 'id', ['label']) !!}
                            </select>
                            <small class="form-text text-muted">
                                Case Material description
                            </small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="ammunition_condition_id" class="form-control-label">Condition</label>
                            <select class="form-control" id="ammunition_condition_id" name="ammunition_condition_id">
                                <option>- Select One -</option>
                                {!! FormHelper::select(AmmunitionCondition::all(), 'id', ['label']) !!}
                            </select>
                            <small class="form-text text-muted">
                                Ammunition Condition description
                            </small>
                        </div>

                        <div class="col-sm-3">
                            <label for="primer_type_id" class="form-control-label">Primer Type</label>
                            @if($caliber->caliberType->id === CaliberType::RIMFIRE)
                                <input type="text" readonly class="form-control-plaintext" value="Rimfire">
                                <input type="hidden" id="primer_type_id" name="primer_type_id" value="{{ PrimerType::RIMFIRE }}">
                            @else
                                <select class="form-control" id="primer_type_id" name="primer_type_id">
                                    <option>- Select One -</option>
                                    {!! FormHelper::select(PrimerType::all(), 'id', ['label']) !!}
                                </select>
                            @endif
                            <small class="form-text text-muted">
                                Primer Type description
                            </small>
                        </div>
                    </div>
                </fieldset>
                @endif

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add New</button>
                </div>
            </form>

        </div>
    </div>

@endsection
