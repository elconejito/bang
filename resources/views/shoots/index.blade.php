@extends('layouts.master')

@section('title', 'Shoots')

@section('content')
    <div class="container">

        <h1>Shoots</h1>
        <p><a href="{{ route('shoots.create') }}"><i class="fa fa-plus"></i> Add New</a></p>
        @if ( $shoots->isEmpty() )
            <p>No Orders yet.</p>
            <p><a href="{{ route('shoots.create') }}"><i class="fa fa-plus"></i> Add the first one</a></p>
        @else
            <table class="table table-hover">
                <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Rounds</th>
                        <th>Range</th>
                        <th>Bullet</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $shoots as $shoot )
                    <tr>
                        <td scope="row">1</td>
                        <td>{{ $shoot->rounds }}</td>
                        <td>{{ $shoot->range }}</td>
                        <td>{{ $shoot->bullet->model }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('shoots.edit', $shoot->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i></a>
                                <a href="{{ route('shoots.destroy', $shoot->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div><!-- /.container -->
@endsection
