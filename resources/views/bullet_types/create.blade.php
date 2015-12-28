@extends('layouts.master')

@section('title', 'New | Bullet Types')

@section('content')
    <div class="container">

        <h1>Create Bullet Type</h1>
        <form action="{{ route('bullet-type.store') }}" method="post" name="bullet-type-create" class="form-inline">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="size" class="sr-only">Size</label>
                <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge">
            </div>
            <button type="submit" class="btn btn-primary">Add New</button>
        </form>
    </div><!-- /.container -->
@endsection
