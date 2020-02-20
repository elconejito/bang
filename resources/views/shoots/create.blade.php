@extends('layouts.master')

@section('title', 'New | Shoot')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'New Shoot',
        'breadcrumbName' => 'shoots.create',
        'breadcrumbParams' => $trip,
        'hasButton' => false,
    ])

    <form action="{{ route('trips.shoots.store', $trip->id) }}" method="post" name="shoot-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="rounds" class="col-sm-2 form-control-label">Rounds Fired</label>
            <div class="col-sm-2">
                <input type="text" class="form-control" id="rounds" name="rounds">
            </div>
        </div>
        <div class="form-group row">
            <label for="firearm_id" class="col-sm-2 form-control-label">Firearm</label>
            <div class="col-sm-10">
                <select class="form-control" id="firearm_id" name="firearm_id">
                    {!! \App\Helpers\FormHelper::select(\App\Firearm::orderBy('manufacturer', 'asc')->orderBy('model', 'asc')->get(), 'id', ['manufacturer', 'model']) !!}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
            <div class="col-sm-10">
                <select class="form-control" id="bullet_id" name="bullet_id">
                    {!! \App\Helpers\FormHelper::select(\App\Bullet::orderBy('manufacturer', 'asc')->orderBy('name', 'asc')->get(), 'id', 'getLabel') !!}
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
