@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="container">

        <h1>Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <h2>Bullet Types</h2>
                <ul class="list-unstyled">
                    <li>Create</li>
                    <li><a href="{{ route('bullet-type.index') }}">Index</a></li>
                </ul>
            </div>
        </div>
    </div><!-- /.container -->
@endsection
