<?php

// Home
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('home'));
});

/*
 * Range Trips
 */
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
    $breadcrumbs->parent('trip', $trip);
    $breadcrumbs->push('Edit '.$trip->trip_date->toDateString(), route('trips.edit', $trip->id));
});

/*
 * Shoots
 */
// Home > Range Trips > Trip > [Shoot]
Breadcrumbs::register('shoot', function($breadcrumbs, $shoot)
{
    $breadcrumbs->parent('trip', $shoot->trip);
    $breadcrumbs->push($shoot->id, route('trips.shoots.show', [$shoot->trip->id, $shoot->id]));
});
// Home > Range Trips > Trip > Shoot > [Edit]
Breadcrumbs::register('shootEdit', function($breadcrumbs, $shoot)
{
    $breadcrumbs->parent('shoot', $shoot);
    $breadcrumbs->push('Edit '.$shoot->id, route('trips.shoots.show', [$shoot->trip->id, $shoot->id]));
});

/*
 * Cartridges
 */
 // Home > Cartridges
Breadcrumbs::register('cartridges', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Cartridges', route('cartridges.index'));
});

/*
 * Purposes
 */
 // Home > Purposes
Breadcrumbs::register('purposes', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Purposes', route('purposes.index'));
});

/*
 * Ranges
 */
 // Home > Ranges
Breadcrumbs::register('ranges', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Ranges', route('ranges.index'));
});