@extends('layouts.master')

@section('title', 'Edit | Cartridge')

@section('content')
    <div class="container">

        <h1>Edit Cartridge</h1>
        <form action="{{ route('cartridges.update', $cartridge->id) }}" method="post" name="cartridge-edit" class="form-inline">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            <div class="form-group">
                <label for="size" class="sr-only">Size</label>
                <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge" value="{{ $cartridge->size }}">
            </div>
            <div class="form-group">
                <label for="label" class="sr-only">Label</label>
                <input type="text" class="form-control" id="label" name="label" placeholder="Label for Cartridge" value="{{ $cartridge->label }}">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

    </div><!-- /.container -->
@endsection
