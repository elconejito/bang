@extends('layouts.master')

@section('title', 'New | Shoot')

@section('content')
    <div class="container">

        <h1>Create Shoot</h1>
        <form action="{{ route('shoots.store') }}" method="post" name="shoot-create">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="rounds" class="col-sm-2 form-control-label">Rounds Fired</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="rounds" name="rounds">
                </div>
            </div>
            <div class="form-group row">
                <label for="range" class="col-sm-2 form-control-label">Range</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="range" name="range" placeholder="Range">
                </div>
            </div>
            <div class="form-group row">
                <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
                <div class="col-sm-10">
                    <select class="form-control" id="bullet_id" name="bullet_id">
                        {!! \App\Helpers\FormHelper::select(\App\Bullet::all(), 'id', 'model') !!}
                    </select>
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
