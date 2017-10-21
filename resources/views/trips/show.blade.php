@extends('layouts.master')

@section('title', 'Show | Range Trip')

@section('content')

    <div class="row">
        <div class="col page-header">
            {!! Breadcrumbs::render('trips.show', $trip) !!}
            <div class="btn-toolbar pull-right" role="toolbar">
                <div class="btn-group" role="group" aria-label="Trip Actions">
                    <a href="{{ route('trips.shoots.create', $trip->id) }}" class="btn btn-success-outline"><i class="fa fa-plus"></i> Add New Shoot</a>
                </div>
                <div class="btn-group" role="group" aria-label="Trip Actions">
                    <a href="{{ route('trips.edit', $trip->id) }}" class="btn btn-secondary"><i class="fa fa-pencil"></i> Edit Trip</a>
                    <a href="{{ route('trips.destroy', $trip->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </div>
            </div>
            <h1><small>Range Trip</small><br />{{ $trip->range->label }} - {{ $trip->trip_date->toFormattedDateString() }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Total Fired</h4>
                    <div class="rounds"><span>{{ $trip->shoots->sum('rounds') }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Trip Date:</h5>
                    <p class="card-text">{{ $trip->trip_date->toFormattedDateString() }}</p>
                    <h5>Range:</h5>
                    <p class="card-text">{{ $trip->range->label  }}</p>
                    <h5>Firearms:</h5>
                    <p class="card-text">
                        <?php
                        $trip->shoots()->get()->unique('firearm_id')->each(function($item, $key) {
                            echo ' <span class="label label-default">'.$item->firearm->label.'</span> ';
                        });
                        ?>
                    </p>
                    <h5>Notes:</h5>
                    <p class="card-text">{{ $trip->notes }}</p>
                </div>
            </div>
            <div class="card has-pictures">
                <div class="card-header">
                    <h4 class="card-title">Targets</h4>
                </div>
                <div class="card-body">
                    <div class="picture-main">
                        @if($trip->targets->isEmpty())
                            <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid img-thumbnail" alt="No Picture Yet">
                        @else
                            <img src="{{ asset($trip->targets->first()->picture->getPath('medium')) }}" class="img-fluid img-thumbnail" alt="{{ $trip->targets->first()->picture->name }}">
                        @endif
                    </div>
                    <div class="pictures-thumbnails row">
                        @foreach($trip->targets as $target)
                            <div class="thumbnail col-6 col-lg-4">
                                <img src="{{ asset($target->picture->getPath()) }}" class="img-thumbnail" alt="{{ $target->picture->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            @if ( $trip->shoots->isEmpty() )
            <p>No Shoots yet.</p>
            @else
            <table class="table table-hover">
                <thead class="thead-default">
                    <tr>
                        <th>#</th>
                        <th>Rounds</th>
                        <th>Firearm</th>
                        <th>Bullet</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $trip->shoots()->get() as $shoot )
                    <tr>
                        <td scope="row">
                            <div class="btn-group">
                                <a href="{{ route('trips.shoots.show', [$trip->id, $shoot->id]) }}" class="btn btn-info btn-sm">View</a>
                                <button type="button" class="btn btn-secondary btn-sm" id="shoot-menu-{{ $shoot->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="shoot-menu-{{ $shoot->id }}">
                                    <a href="{{ route('trips.shoots.edit', [$trip->id, $shoot->id]) }}" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                    <form action="{{ action('ShootController@destroy', [$trip->id, $shoot->id]) }}" method="POST">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
                                        <button type="submit" class="dropdown-item"><i class="fa fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                        <td>{{ $shoot->rounds }}</td>
                        <td><a href="{{ route('firearms.show', $shoot->firearm->id) }}">{{ $shoot->firearm->label }}</a> <a href="{{ route('shootsFirearms', $shoot->firearm->id) }}"><i class="fa fa-search fa-border" aria-hidden="true"></i></a></td>
                        <td><a href="{{ route('cartridges.bullets.show', [$shoot->bullet->cartridge->id, $shoot->bullet->id]) }}">{{ $shoot->bullet->getLabel() }}</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        </div>
    </div>

@endsection
