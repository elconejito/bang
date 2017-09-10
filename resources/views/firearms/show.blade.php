@extends('layouts.master')

@section('title', 'Show | Firearm')

@section('content')
    {!! Breadcrumbs::render('firearm', $firearm) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Firearm Actions">
            <a href="{{ route('firearms.edit', $firearm->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Firearm</a>
            <a href="{{ route('firearms.destroy', $firearm->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <h1><small>Firearm</small><br />{{ $firearm->label }}</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Total Fired</h4>
                    <div class="rounds"><span>{{ $firearm->totalRoundsFired() }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Manufacturer:</h5>
                    <p class="card-text">{{ $firearm->manufacturer }}</p>
                    <h5>Model:</h5>
                    <p class="card-text">{{ $firearm->model }}</p>
                    <h5>Cartridge:</h5>
                    <p class="card-text">{{ $firearm->cartridge->size }}</p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Photos <span class="label label-default">0</span></h4>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid" alt="Card image cap">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h4>Notes:</h4>
            <p>{{ $firearm->notes }}</p>
        </div>
    </div>
@endsection
