@extends('layouts.master')

@section('title', 'New | Cartridge')

@section('content')
    <div class="container">

        <h1>Create Cartridge</h1>
        <form action="{{ route('cartridges.store') }}" method="post" name="cartridge-create" class="form-inline">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="size" class="sr-only">Size</label>
                <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge">
            </div>
            <div class="form-group">
                <label for="label" class="sr-only">Label</label>
                <input type="text" class="form-control" id="label" name="label" placeholder="Label for Cartridge">
            </div>
            <button type="submit" class="btn btn-primary">Add New</button>
        </form>
    </div><!-- /.container -->
@endsection
