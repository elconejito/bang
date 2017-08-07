@extends('layouts.master')

@section('title', 'Show | Store')

@section('content')
    {!! Breadcrumbs::render('store', $store) !!}
    <h1>Show Store</h1>
@endsection
