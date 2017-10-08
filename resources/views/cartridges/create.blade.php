@extends('layouts.master')

@section('title', 'New | Cartridge')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Create Cartridge',
        'breadcrumbName' => 'cartridges.create',
        'breadcrumbParams' => null,
        'hasButton' => false
    ])

    <form action="{{ route('cartridges.store') }}" method="post" name="cartridge-create">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="size">Size</label>
            <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge">
        </div>
        <div class="form-group">
            <label for="label">Label</label>
            <input type="text" class="form-control" id="label" name="label" placeholder="Label for Cartridge">
        </div>
        <button type="submit" class="btn btn-primary">Add New</button>
    </form>

@endsection
