@extends('layouts.master')

@section('title', 'Show | Magazine')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => '<small>'. $magazine->label .'</small><br />' . $magazine->manufacturer . ' ' . $magazine->model_name,
        'breadcrumbName' => 'magazines.show',
        'breadcrumbParams' => $magazine,
        'hasButton' => true,
        'buttonLink' => route('magazines.edit', [$magazine->id]),
        'buttonRouteParams' => $magazine,
        'buttonText' => 'Edit Magazine'
    ])

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
                    <h5>Caliber(s):</h5>
                    <p class="card-text">
                        @foreach($magazine->calibers as $caliber)
                            <span class="badge badge-secondary">{{ $caliber->short_label }}</span>
                        @endforeach
                    </p>
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

