@extends('layouts.master')

@section('title', 'New | Range')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'New Range',
        'breadcrumbName' => 'ranges.create',
        'breadcrumbParams' => null,
        'hasButton' => false,
    ])

    <form action="{{ route('ranges.store') }}" method="post" name="range-create">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="label" class="col-sm-2 form-control-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label">
            </div>
        </div>
        <div class="form-group row">
            <label for="notes" class="col-sm-2 form-control-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Add New</button>
            </div>
        </div>
    </form>
@endsection
