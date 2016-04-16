<?php

// Home
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('home'));
});

// Home > Range Trips
Breadcrumbs::register('trips', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Range Trips', route('trips.index'));
});

// Home > Range Trips > [Trip]
Breadcrumbs::register('trip', function($breadcrumbs, $trip)
{
    $breadcrumbs->parent('trips');
    $breadcrumbs->push($trip->trip_date->toDateString(), route('trips.show', $trip->id));
});

// Home > Range Trips > [Create]
Breadcrumbs::register('tripCreate', function($breadcrumbs)
{
    $breadcrumbs->parent('trips');
    $breadcrumbs->push('Create', route('trips.create'));
});

// Home > Range Trips > Trip > [Edit]
Breadcrumbs::register('tripEdit', function($breadcrumbs, $trip)
{
    $breadcrumbs->parent('trip');
    $breadcrumbs->push($trip->trip_date->toDateString(), route('trips.show', $trip->id));
});
