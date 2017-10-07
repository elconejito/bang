
@extends('layouts.master')

@section('title', 'Magazines')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => 'Magazines',
        'breadcrumbName' => 'magazines',
        'breadcrumbParams' => null,
        'hasButton' => true,
        'buttonLink' => route('magazines.create'),
        'buttonRouteParams' => null,
        'buttonText' => 'Add New Magazine'
    ])

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Label</th>
                <th>Manufacturer</th>
                <th>Model Name</th>
                <th>Cartridge</th>
                <th>Capacity</th>
                <th>Serial Number</th>
                <th>ID Marking</th>
            </tr>
        </thead>

        <tbody>

        @if ( $magazines->isEmpty() )
            <tr>
                <td>No Magazines yet.</td>
            </tr>
        @else
            @foreach ( $magazines as $magazine )
                <tr>
                    <td><a href="{{ route('magazines.show', $magazine->id) }}">{{ $magazine->label }}</a></td>
                    <td>{{ $magazine->manufacturer }}</td>
                    <td>{{ $magazine->model_name }}</td>
                    <td>{{ $magazine->cartridge->label }}</td>
                    <td>{{ $magazine->capacity }}</td>
                    <td>{{ $magazine->serial_number }}</td>
                    <td>{{ $magazine->id_marking }}</td>
                </tr>
            @endforeach
        @endif

        </tbody>
    </table>
@endsection
