@extends('layouts.master')

@section('title', 'Edit | Cartridge')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => '<small>Edit Cartridge</small><br />'. $cartridge->size,
        'breadcrumbName' => 'cartridges.edit',
        'breadcrumbParams' => $cartridge,
        'hasButton' => false
    ])

    <form action="{{ route('cartridges.update', $cartridge->id) }}" method="post" name="cartridge-edit">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="put" />

        <div class="form-group row">
            <label for="size" class="col-sm-2 col-form-label">Size</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge" value="{{ $cartridge->size }}">
                <small class="form-text text-muted">
                    The actual size of the cartridge such as 9mm, 7.62x39mm, .308, or .30-06, etc
                </small>
            </div>
        </div>

        <div class="form-group row">
            <label for="label" class="col-sm-2 col-form-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label for Cartridge" value="{{ $cartridge->label }}">
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
