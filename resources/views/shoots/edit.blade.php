@extends('layouts.master')

@section('title', 'Edit | Shoot')

@section('content')
    <div class="container">

        <h1>Edit Shoot</h1>
        <form action="{{ route('shoots.update', $shoot->id) }}" method="post" name="shoot-edit">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put" />
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="rounds" class="col-sm-2 form-control-label">Rounds</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="rounds" name="rounds" value="{{ $shoot->rounds }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="range" class="col-sm-2 form-control-label">Range</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="range" name="range" placeholder="Range" value="{{ $shoot->range }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="bullet_id" class="col-sm-2 form-control-label">Bullet</label>
                <div class="col-sm-10">
                    <select class="form-control" id="bullet_id" name="bullet_id">
                        {!! \App\Helpers\FormHelper::select(\App\Bullet::all(), 'id', 'model', $shoot->bullet_id) !!}
                    </select>
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
