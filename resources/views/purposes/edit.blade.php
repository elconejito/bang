@extends('layouts.master')

@section('title', 'Edit | Purpose')

@section('content')
    <div class="container">

        <h1>Edit Purpose</h1>
        <form action="{{ route('purposes.update', $purpose->id) }}" method="post" name="use-edit">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            <div class="form-group row">
                <label for="label" class="col-sm-2 form-control-label">Label</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="label" name="label" placeholder="Label" value="{{ $purpose->label }}">
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>

    </div><!-- /.container -->
@endsection
