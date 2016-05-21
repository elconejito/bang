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
            <div class="card card-primary-outline">
                <div class="card-block card-flex">
                    <div class="rounds"><span>{{ $firearm->totalRoundsFired() }}</span>rnds</div>
                    <p>Total Fired</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Manufacturer</strong>: <span class="pull-xs-right">{{ $firearm->manufacturer }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Model</strong>: <span class="pull-xs-right">{{ $firearm->model }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Cartridge</strong>: <span class="pull-xs-right">{{ $firearm->cartridge->size }}</span>
                    </li>
                </ul>
            </div>
            <div class="card card-primary-outline">
                <div class="card-block">
                    <h4 class="card-title">Photos <span class="label label-default">0</span></h4>
                </div>
                <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid" alt="Card image cap">
            </div>
        </div>
        <div class="col-md-8">
            <h4>Notes:</h4>
            <p>{{ $firearm->notes }}</p>
        </div>
    </div>
@endsection
