@extends('layouts.master')

@section('title', 'Edit | Bullet')

@section('content')
    <div class="container">

        <h1>Edit Bullet</h1>
        <form action="{{ route('bullets.update', $bullet->id) }}" method="post" name="bullet-edit" class="form-inline">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            <div class="form-group">
                <label for="size" class="sr-only">Size</label>
                <input type="text" class="form-control" id="size" name="size" placeholder="Size of Cartridge" value="{{ $bullet->size }}">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

    </div><!-- /.container -->
@endsection
