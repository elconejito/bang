<?php

/**
 * Home Dashboard
 */
Breadcrumbs::for('home', function($trail)
{
    $trail->push('Home', route('home'));
});


/**
 * Auth
 */
Breadcrumbs::for('login', function($trail)
{
    $trail->push('Login', route('login'));
});
Breadcrumbs::for('register', function($trail)
{
    $trail->push('Register', route('register'));
});
Breadcrumbs::for('password.email', function($trail)
{
    $trail->push('Password Reset', route('password.email'));
});
Breadcrumbs::for('password.reset', function($trail, $token)
{
    $trail->push('Password Reset', route('password.reset', $token));
});


/**
 * Range Trips
 */
// Home > Range Trips
Breadcrumbs::for('trips', function($trail)
{
    $trail->parent('home');
    $trail->push('Range Trips', route('trips.index'));
});
// Home > Range Trips > [Trip]
Breadcrumbs::for('trips.show', function($trail, $trip)
{
    $trail->parent('trips');
    $trail->push($trip->trip_date->toDateString(), route('trips.show', $trip->id));
});
// Home > Range Trips > [Create]
Breadcrumbs::for('trips.create', function($trail)
{
    $trail->parent('trips');
    $trail->push('Create', route('trips.create'));
});
// Home > Range Trips > Trip > [Edit]
Breadcrumbs::for('trips.edit', function($trail, $trip)
{
    $trail->parent('trip', $trip);
    $trail->push('Edit '.$trip->trip_date->toDateString(), route('trips.edit', $trip->id));
});

/**
 * Shoots
 */
// Home > Range Trips > Trip > [Shoot]
Breadcrumbs::for('shoots', function($trail, $shoot)
{
    $trail->parent('trips.show', $shoot->trip);
    $trail->push($shoot->id, route('trips.shoots.show', [$shoot->trip->id, $shoot->id]));
});
// Home > Range Trips > Trip > Shoot > [Edit]
Breadcrumbs::for('shoots.edit', function($trail, $shoot)
{
    $trail->parent('shoots', $shoot);
    $trail->push('Edit '.$shoot->id, route('trips.shoots.show', [$shoot->trip->id, $shoot->id]));
});
// Home > Range Trips > Trip > Shoot > [Create]
Breadcrumbs::for('shoots.create', function($trail, $trip)
{
    $trail->parent('trips.show', $trip);
    $trail->push('Create', route('trips.shoots.create', $trip->id));
});


/**
 * Calibers
 **/
// Home > Calibers
Breadcrumbs::for('calibers', function($trail)
{
    $trail->parent('home');
    $trail->push('Calibers', route('calibers.index'));
});
// Home > Calibers > Create
Breadcrumbs::for('calibers.create', function($trail)
{
    $trail->parent('calibers');
    $trail->push('New', route('calibers.create'));
});
// Home > Calibers > Cartridge
Breadcrumbs::for('calibers.show', function($trail, $cartridge)
{
    $trail->parent('calibers');
    $trail->push($cartridge->caliber, route('calibers.show', $cartridge));
});
// Home > Calibers > Cartridge > Edit
Breadcrumbs::for('calibers.edit', function($trail, $cartridge)
{
    $trail->parent('calibers.show', $cartridge);
    $trail->push('Edit '.$cartridge->caliber, route('calibers.edit', $cartridge));
});


/**
 * Ammunitions
 **/
// Home > Ammunitions
Breadcrumbs::for('ammunitions', function($trail, $caliber)
{
    $trail->parent('calibers');
    $trail->push($caliber->short_label . ' Ammunition', route('calibers.ammunitions.index', $caliber->id));
});
// Home > Ammunitions > Create
Breadcrumbs::for('ammunitions.create', function($trail, $caliber)
{
    $trail->parent('ammunitions', $caliber);
    $trail->push('New', route('calibers.create'));
});
// Home > Ammunitions > Ammunition
Breadcrumbs::for('ammunition', function($trail, $ammunition)
{
    $trail->parent('ammunitions', $ammunition->caliber);
    $trail->push($ammunition->getLabel(), route('calibers.ammunitions.show', [$ammunition->caliber->id, $ammunition->id]));
});
// Home > Ammunitions > Ammunition
Breadcrumbs::for('ammunition.edit', function($trail, $ammunition)
{
    $trail->parent('ammunition', $ammunition);
    $trail->push('Edit', route('calibers.ammunitions.edit', [$ammunition->caliber->id, $ammunition->id]));
});


/**
 * Magazines
 **/
// Home > Magazines
Breadcrumbs::for('magazines', function($trail)
{
    $trail->parent('home');
    $trail->push('Magazines', route('magazines.index'));
});

// Home > Magazines > Create
Breadcrumbs::for('magazines.create', function($trail)
{
    $trail->parent('magazines');
    $trail->push('New', route('magazines.create'));
});

// Home > Magazines > Magazine
Breadcrumbs::for('magazines.show', function($trail, $magazine)
{
    $trail->parent('magazines');
    $trail->push($magazine->label, route('magazines.show', $magazine->id));
});


/**
 * Targets
 **/
// Home > Target
Breadcrumbs::for('targets', function($trail)
{
    $trail->parent('home');
    $trail->push('Targets', route('targets.index'));
});

// Home > Targets > Create
Breadcrumbs::for('targets.create', function($trail)
{
    $trail->parent('targets');
    $trail->push('New', route('targets.create'));
});

// Home > Targets > Target
Breadcrumbs::for('targets.show', function($trail, $target)
{
    $trail->parent('targets');
    $trail->push($target->label, route('targets.show', $target->id));
});


/**
 * Purposes
 */
// Home > Purposes
Breadcrumbs::for('purposes', function($trail)
{
    $trail->parent('home');
    $trail->push('Purposes', route('purposes.index'));
});


/**
 * Ranges
 */
// Home > Ranges
Breadcrumbs::for('ranges', function($trail)
{
    $trail->parent('home');
    $trail->push('Ranges', route('ranges.index'));
});

// Home > Ranges > Create
Breadcrumbs::for('ranges.create', function($trail)
{
    $trail->parent('ranges');
    $trail->push('New', route('ranges.create'));
});

// Home > Ranges > Range
Breadcrumbs::for('ranges.show', function($trail, $range)
{
    $trail->parent('ranges');
    $trail->push($range->label, route('ranges.show', $range->id));
});

// Home > Ranges > Range > [Edit]
Breadcrumbs::for('ranges.edit', function($trail, $range)
{
    $trail->parent('ranges.show', $range);
    $trail->push('Edit', route('ranges.edit', $range->id));
});


/**
 * Stores
 */
// Home > Stores
Breadcrumbs::for('stores', function($trail)
{
    $trail->parent('home');
    $trail->push('Stores', route('stores.index'));
});
// Home > Stores > Create
Breadcrumbs::for('stores.create', function($trail)
{
    $trail->parent('stores');
    $trail->push('New', route('stores.create'));
});
// Home > Stores > Store
Breadcrumbs::for('stores.show', function($trail, $store)
{
    $trail->parent('stores');
    $trail->push($store->label, route('stores.show', $store->id));
});


/**
 * Orders
 */
// Home > Orders
Breadcrumbs::for('orders', function($trail)
{
    $trail->parent('home');
    $trail->push('Orders', route('orders.index'));
});
// Home > Orders > Order
Breadcrumbs::for('orders.show', function($trail, $order)
{
    $trail->parent('orders');
    $trail->push($order->order_date->toDateString(), route('orders.show', $order->id));
});
// Home > Orders > [Create]
Breadcrumbs::for('orders.create', function($trail)
{
    $trail->parent('orders');
    $trail->push('New', route('orders.create'));
});
// Home > Orders > Order > [Edit]
Breadcrumbs::for('orders.edit', function($trail, $order)
{
    $trail->parent('orders.show', $order);
    $trail->push('Edit '.$order->id, route('orders.edit', $order->id));
});


/**
 * Inventories
 */
// Home > Orders > Order > Inventory
Breadcrumbs::for('inventory', function($trail, $inventory)
{
    $trail->parent('orders.show', $inventory->order);
    $trail->push($inventory->id, route('orders.inventories.show', [$inventory->order->id, $inventory->id]));
});
// Home > Orders > Order > Inventory > [Edit]
Breadcrumbs::for('inventories.edit', function($trail, $inventory)
{
    $trail->parent('inventory', $inventory);
    $trail->push('Edit '.$inventory->id, route('orders.inventories.edit', [$inventory->order->id, $inventory->id]));
});
// Home > Orders > Order > Inventory > [Create]
Breadcrumbs::for('inventories.create', function($trail, $order)
{
    $trail->parent('orders.show', $order);
    $trail->push('Create', route('orders.inventories.create', $order->id));
});


/**
 * Firearms
 */
// Home > Firearms
Breadcrumbs::for('firearms', function($trail)
{
    $trail->parent('home');
    $trail->push('Firearms', route('firearms.index'));
});
// Home > Firearms > Firearm
Breadcrumbs::for('firearms.show', function($trail, $firearm)
{
    $trail->parent('firearms');
    $trail->push($firearm->label, route('firearms.show', $firearm->id));
});
// Home > Firearms > Firearm > [Edit]
Breadcrumbs::for('firearms.edit', function($trail, $firearm)
{
    $trail->parent('firearms.show', $firearm);
    $trail->push('Edit '.$firearm->label, route('firearms.edit', $firearm->id));
});
// Home > Firearms > Firearm
Breadcrumbs::for('firearms.create', function($trail)
{
    $trail->parent('firearms');
    $trail->push('Create', route('firearms.create'));
});
