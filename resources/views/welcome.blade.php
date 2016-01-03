@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="container">

        <h1>Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <h2>Cartridges</h2>
                <ul class="list-unstyled">
                    <li><a href="{{ route('cartridges.create') }}">Create</a></li>
                    <li><a href="{{ route('cartridges.index') }}">Index</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h2>Bullets</h2>
                <ul class="list-unstyled">
                    <li><a href="{{ route('bullets.create') }}">Create</a></li>
                    <li><a href="{{ route('bullets.index') }}">Index</a></li>
                </ul>
            </div>
        </div>
    </div><!-- /.container -->
@endsection
