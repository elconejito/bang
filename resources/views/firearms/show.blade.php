@extends('layouts.master')

@section('title', $firearm->label . ' | Firearm')

@section('content')

    @include('layouts.partials.page-header', [
        'pageTitle' => '<small>'. $firearm->label .'</small><br />' . $firearm->manufacturer . ' ' . $firearm->model,
        'breadcrumbName' => 'firearms.show',
        'breadcrumbParams' => $firearm,
        'hasButton' => true,
        'buttonLink' => route('firearms.edit', [$firearm->id]),
        'buttonRouteParams' => $firearm,
        'buttonText' => 'Edit Firearm'
    ])

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Total Fired</h4>
                    <div class="rounds"><span>{{ $firearm->totalRoundsFired() }}</span>rnds</div>
                </div>
                <div class="card-body">
                    <h5>Manufacturer:</h5>
                    <p class="card-text">{{ $firearm->manufacturer }}</p>
                    <h5>Model:</h5>
                    <p class="card-text">{{ $firearm->model }}</p>
                    <h5>Caliber(s):</h5>
                    <p class="card-text">
                        @foreach($firearm->calibers as $caliber)
                            <span class="badge badge-secondary">{{ $caliber->short_label }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Photos <span class="label label-default">0</span></h4>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid" alt="Card image cap">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card has-pictures">
                <div class="card-header">
                    <h4 class="card-title">Targets</h4>
                </div>
                <div class="card-body">
                    <div class="picture-main">
                        @if($firearm->targets->isEmpty())
                            <img src="{{ asset('assets/images/no-image_350x200.png') }}" class="img-fluid img-thumbnail" alt="No Picture Yet">
                        @else
                            <a href="{{ route('targets.show', $firearm->targets->first()->id) }}">
                                <img src="{{ asset($firearm->targets->first()->picture->getPath('medium')) }}" class="img-fluid img-thumbnail" alt="{{ $firearm->targets->first()->picture->name }}">
                            </a>
                        @endif
                    </div>
                    <div class="pictures-thumbnails row">
                        @foreach($firearm->targets as $target)
                            <div class="thumbnail col-6 col-lg-4">
                                <img src="{{ asset($target->picture->getPath()) }}" class="img-thumbnail" alt="{{ $target->picture->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h4>Notes:</h4>
            @if($firearm->notes->isEmpty())
                <p>There are no Notes</p>
            @endif
            @foreach($firearm->notes as $note)
                <ul>
                    <li>
                        <p class="small">
                            {{ $note->created_at->format() }}
                        </p>
                        <p>
                            {{ $note->note }}
                        </p>
                    </li>
                </ul>
            @endforeach
        </div>
    </div>
@endsection
