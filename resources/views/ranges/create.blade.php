@extends('layouts.master')

@section('title', 'New | Range')

@section('content')
    <div class="container">

        <h1>Create Range</h1>
        <form action="{{ route('ranges.store') }}" method="post" name="use-create">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="label" class="col-sm-2 form-control-label">Label</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="label" name="label" placeholder="Label">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Add New</button>
                </div>
            </div>
        </form>
    </div><!-- /.container -->
@endsection
