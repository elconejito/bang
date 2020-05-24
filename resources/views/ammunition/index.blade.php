@php
use App\Bullet;
use App\Models\Caliber;
@endphp

@extends('layouts.master')

@section('title', 'Bullet')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => $caliber->short_label.' Ammunition',
        'breadcrumbName' => 'ammunitions',
        'breadcrumbParams' => $caliber,
        'hasButton' => true,
        'buttonLink' => route('calibers.ammunitions.create', $caliber),
        'buttonRouteParams' => $caliber,
        'buttonText' => 'Add New Ammunition',
        'buttonTextIcon' => 'fas fa-plus'
    ])

    <div class="row">
    @if ( $ammunitions->isEmpty() )
        <div class="col">
            <p>No Ammunition yet.</p>
        </div>
    @else
        @foreach( $ammunitions as $ammunition )
        <div class="col-sm-6 col-lg-4">
            <div class="card border-dark">
                <div class="card-header">
                    <h4 class="card-title">
                        <small>{{ $ammunition->manufacturer }}</small><br />
                        <a href="{{ route('calibers.ammunitions.show', [$caliber->id, $ammunition->id]) }}">{{ $ammunition->name }}</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="rounds"><span>{{ $ammunition->inventory }}</span>rnds</div>
                    <p class="card-text">
                        <a href="#"><span class="badge badge-dark">{{ $ammunition->caliber->size }}</span></a>
                        <a href="#"><span class="badge badge-dark">{{ $ammunition->purpose->label }}</span></a>
                        <a href="#"><span class="badge badge-dark">{{ $ammunition->weight }}gr</span></a>
                    </p>
                </div>
                <div class="card-footer text-muted">
                    <a class="card-link" href="{{ route('calibers.ammunitions.edit', [$caliber->id, $ammunition->id]) }}">Edit</a>
                    <a class="card-link" href="{{ route('calibers.ammunitionss.destroy', [$caliber->id, $ammunition->id]) }}">Delete</a>
                    <hr />
                    <span class="card-link">Orders</span>
                    <span class="card-link">Shoots</span>
                </div>
            </div>
        </div>
        @endforeach
    @endif
    </div>


    @if($ammunitions_inactive)
        <h3 class="text-muted">Inactive</h3>
        <div class="row">
            @foreach( $ammunitions_inactive as $ammunition )
            <div class="col-sm-6 col-lg-4">
                <div class="card border-light inactive">
                    <div class="card-header">
                        <h4 class="card-title">
                            <small>{{ $ammunition->manufacturer }}</small><br />
                            <a href="{{ route('calibers.ammunitions.show', [$caliber->id, $ammunition->id]) }}">{{ $ammunition->name }}</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="rounds"><span>{{ $ammunition->inventory }}</span>rnds</div>
                        <p class="card-text">
                            <a href="#"><span class="badge badge-dark">{{ $ammunition->caliber->size }}</span></a>
                            <a href="#"><span class="badge badge-dark">{{ $ammunition->purpose->label }}</span></a>
                            <a href="#"><span class="badge badge-dark">{{ $ammunition->weight }}gr</span></a>
                        </p>
                    </div>
                    <div class="card-footer text-muted">
                        <a class="card-link" href="{{ route('calibers.ammunitions.edit', [$caliber->id, $ammunition->id]) }}">Edit</a>
                        <a class="card-link" href="{{ route('calibers.ammunitions.destroy', [$caliber->id, $ammunition->id]) }}">Delete</a>
                        <hr />
                        <span class="card-link">Orders</span>
                        <span class="card-link">Shoots</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@endsection
