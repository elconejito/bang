@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Dashboard',
        'breadcrumbName' => 'home',
        'breadcrumbParams' => null,
        'hasButton' => false
    ])

    <div class="row">
        <div class="col">
            <p>Welcome.</p>
        </div>
    </div>

@endsection
