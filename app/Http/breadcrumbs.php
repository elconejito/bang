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
// Home > Ranges > Range
Breadcrumbs::register('range', function($breadcrumbs, $range)
{
    $breadcrumbs->parent('ranges');
    $breadcrumbs->push($range->label, route('ranges.show', $range->id));
});

/*
 * Stores
 */
// Home > Stores
Breadcrumbs::register('stores', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Stores', route('stores.index'));
});
// Home > Stores > Store
Breadcrumbs::register('store', function($breadcrumbs, $store)
{
    $breadcrumbs->parent('stores');
    $breadcrumbs->push($store->label, route('stores.show', $store->id));
});

/*
 * Orders
 */
// Home > Orders
Breadcrumbs::register('orders', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Orders', route('orders.index'));
});
// Home > Orders > Order
Breadcrumbs::register('order', function($breadcrumbs, $order)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push($order->order_date->toDateString(), route('orders.show', $order->id));
});
// Home > Orders > [Create]
Breadcrumbs::register('orderCreate', function($breadcrumbs)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push('Create', route('orders.create'));
});
// Home > Orders > Order > [Edit]
Breadcrumbs::register('orderEdit', function($breadcrumbs, $order)
{
    $breadcrumbs->parent('order', $order);
    $breadcrumbs->push('Edit '.$order->order_date->toDateString(), route('orders.edit', $order->id));
});

/*
 * Firearms
 */
// Home > Firearms
Breadcrumbs::register('firearms', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Firearms', route('firearms.index'));
});
// Home > Firearms > Firearm
Breadcrumbs::register('firearm', function($breadcrumbs, $firearm)
{
    $breadcrumbs->parent('firearms');
    $breadcrumbs->push($firearm->label, route('firearms.show', $firearm->id));
});

/*
 * Bullets
 */
// Home > Bullets
Breadcrumbs::register('bullets', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Bullets', route('bullets.index'));
});
// Home > Bullets > Bullet
Breadcrumbs::register('bullet', function($breadcrumbs, $bullet)
{
    $breadcrumbs->parent('bullets');
    $breadcrumbs->push($bullet->label, route('bullets.show', $bullet->id));
});