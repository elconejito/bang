@extends('layouts.master')

@section('title', 'Show | Firearm')

@section('content')
    <div class="container">

        <h1><small>Firearm</small><br />{{ $firearm->label }}</h1>
        <div class="row">
            <div class="col-md-6">
                <h4>Manufacturer:</h4>
                <p>{{ $firearm->manufacturer }}</p>
                <h4>Model:</h4>
                <p>{{ $firearm->model }}</p>
                <h4>Cartridge:</h4>
                <p>{{ $firearm->cartridge->size }}</p>
            </div>
            <div class="col-md-6">
                <h4>Notes:</h4>
                <p>{{ $firearm->notes }}</p>
            </div>
        </div>

    </div><!-- /.container -->
@endsection
