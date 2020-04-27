@extends('layouts.master')

@section('title', 'Edit | Range')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Edit Range',
        'breadcrumbName' => 'ranges.edit',
        'breadcrumbParams' => $range,
        'hasButton' => false,
    ])

    <form action="{{ route('ranges.update', $range->id) }}" method="post" name="range-edit">
        @csrf
        @method('PUT')

        <input type="hidden" name="_method" value="put" />
        <div class="form-group row">
            <label for="label" class="col-sm-2 form-control-label">Label</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="label" name="label" placeholder="Label" value="{{ $range->label }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="notes" class="col-sm-2 form-control-label">Notes</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $range->notes }}</textarea>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
@endsection
