@extends('layouts.master')

@section('title', 'Show | Cartridge')

@section('content')
    <div class="container">

        <h1><small>Cartridge</small><br />{{ $cartridge->size }}</h1>

    </div><!-- /.container -->
@endsection
