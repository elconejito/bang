@extends('layouts.master')

@section('title', 'Show | Cartridge')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => '<small>Cartridge</small><br />'. $cartridge->size,
        'breadcrumbName' => 'cartridges.show',
        'breadcrumbParams' => $cartridge,
        'hasButton' => false
    ])
    <div class="row">
        <div class="col">
            <p>Cartridge content, #wip</p>
        </div>
    </div>

@endsection
