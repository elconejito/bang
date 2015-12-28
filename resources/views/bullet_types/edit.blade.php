@extends('layouts.master')

@section('title', 'Edit | Bullet Types')

@section('content')
    <div class="container">

        <h1>Edit Bullet Type</h1>
        <form action="{{ route('bullet-type.update', $bullet_type->id) }}" method="post" name="bullet-type-edit" class="form-inline">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            <div class="form-group">
                <label for="size" class="sr-only">Size</label>
                <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge" value="{{ $bullet_type->size }}">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

    </div><!-- /.container -->
@endsection
