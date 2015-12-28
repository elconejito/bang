@extends('layouts.master')

@section('title', 'Bullet Types')

@section('content')
    <div class="container">

        <h1>Bullet Types</h1>
        <p><a href="{{ route('bullet-type.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $bullet_types->isEmpty() )
            <p>No Bullet Types yet.</p>
            <p><a href="{{ route('bullet-type.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <div class="row">
            @foreach ( $bullet_types as $bullet_type )
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-block">
                            <h4 class="card-title">{{ $bullet_type->size }}</h4>
                        </div>
                        <div class="card-block">
                            <nav class="nav nav-inline">
                                <a class="nav-link" href="{{ route('bullet-type.edit', $bullet_type->id) }}">Edit</a>
                                <a class="nav-link" href="{{ route('bullet-type.destroy', $bullet_type->id) }}">Delete</a>
                            </nav>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif
    </div><!-- /.container -->
@endsection
