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
    <h1><small>Bullet</small><br />{{ $bullet->getLabel('short') }}</h1>
    <div class="row">
        <div class="col-md-4">
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Manufacturer:</strong><br />
                    {{ $bullet->manufacturer }}
                </li>
                <li class="list-group-item">
                    <strong>Model:</strong><br />
                    {{ $bullet->model }}
                </li>
                <li class="list-group-item">
                    <strong>Weight:</strong><br />
                    {{ $bullet->weight }}
                </li>
                <li class="list-group-item">
                    <strong>Cartridge:</strong><br />
                    {{ $bullet->cartridge->size }}
                </li>
                <li class="list-group-item">
                    <strong>Purpose:</strong><br />
                    {{ $bullet->purpose->label }}
                </li>
            </ul>
        </div>
        <div class="col-md-8">
            <h4>Notes:</h4>
            <p></p>
        </div>
    </div>
@endsection
