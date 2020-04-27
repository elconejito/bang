@extends('layouts.master')

@section('title', 'Show | Range')

@section('content')
    @include('layouts.partials.page-header', [
        'pageTitle' => $range->label,
        'breadcrumbName' => 'ranges.show',
        'breadcrumbParams' => $range,
        'hasButton' => true,
        'buttonLink' => route('ranges.edit', $range),
        'buttonText' => 'Edit Range'
    ])

    <div class="row">
        <div class="col">
            <h4>{{ $range->label }}</h4>
        </div>
    </div>

@endsection
