@extends('layouts.master')

@section('title', 'Show | Bullet')

@section('content')
    {!! Breadcrumbs::render('bullet', $bullet) !!}
    <div class="btn-toolbar pull-right" role="toolbar">
        <div class="btn-group" role="group" aria-label="Bullet Actions">
            <a href="{{ route('cartridges.bullets.edit', [$bullet->cartridge->id, $bullet->id]) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Bullet</a>
            <a href="{{ route('cartridges.bullets.destroy', [$bullet->cartridge->id, $bullet->id]) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
    <h1><small>{{ $bullet->manufacturer }}</small><br />{{ $bullet->model }}</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary-outline">
                <div class="card-block card-flex">
                    <div class="rounds"><span>{{ $bullet->inventory }}</span>rnds</div>
                    <p>
                        <strong>Cartridge:</strong> {{ $bullet->cartridge->size }}<br />
                        <strong>Weight:</strong> {{ $bullet->weight }}gr<br />
                        <strong>Purpose:</strong> {{ $bullet->purpose->label }}<br />
                    </p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Manufacturer:</strong><br />
                        {{ $bullet->manufacturer }}
                    </li>
                    <li class="list-group-item">
                        <strong>Notes:</strong><br />
                        {{ $bullet->manufacturer }}
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
        <div class="col-md-4">
            <h4>Orders:</h4>
            <p></p>
        </div>
        <div class="col-md-4">
            <h4>Shoots:</h4>
            <p></p>
        </div>
    </div>
@endsection
