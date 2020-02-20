@extends('layouts.master')

@section('title', 'Welcome')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Welcome',
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
