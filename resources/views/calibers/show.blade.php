@extends('layouts.master')

@section('title', 'Show | Caliber')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => '<small>Caliber</small><br />'. $caliber->size,
        'breadcrumbName' => 'calibers.show',
        'breadcrumbParams' => $caliber,
        'hasButton' => false
    ])
    <div class="row">
        <div class="col">
            <p>Caliber content, #wip</p>
        </div>
    </div>

@endsection
