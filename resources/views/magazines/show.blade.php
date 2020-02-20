@extends('layouts.master')

@section('title', 'Show | Magazine')

@section('content')

    <div class="row">
        <div class="col page-header">
            {!! Breadcrumbs::render('magazines.show', $magazine) !!}
            <div class="btn-toolbar pull-right" role="toolbar">
                <div class="btn-group" role="group" aria-label="Firearm Actions">
                    <a href="{{ route('magazines.edit', $magazine->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Magazine</a>
                    <a href="{{ route('magazines.destroy', $magazine->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </div>
            </div>
            <h1><small>Magazine</small><br />{{ $magazine->label }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Total Fired</h4>
                    <div class="rounds"><span>0</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Manufacturer:</h5>
                    <p class="card-text">{{ $magazine->manufacturer }}</p>
                    <h5>Model Name:</h5>
                    <p class="card-text">{{ $magazine->model_name }}</p>
                    <h5>Capacity:</h5>
                    <p class="card-text">{{ $magazine->capacity }}</p>
                    <h5>Serial Number:</h5>
                    <p class="card-text">{{ $magazine->serial_number }}</p>
                    <h5>ID Marking:</h5>
                    <p class="card-text">{{ $magazine->id_marking }}</p>
                    <h5>Cartridge:</h5>
                    <p class="card-text">{{ $magazine->cartridge->label }}</p>
                </div>
            </div>
            <div class="card has-pictures">
                <div class="card-header">
                    <h4 class="card-title">Photos</h4>
                </div>
                <div class="card-body">
                    @include('layouts.partials.grid-pictures', ['pictures' => $magazine->pictures])

                    <form action="{{ action('MagazineController@addPhoto', $magazine->id) }}" class="dropzone picture-dropzone" id="magazine-photo-dropzone">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            STUFF
        </div>
    </div>
@endsection

