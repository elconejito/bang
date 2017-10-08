<?php

/**
 * Home Dashboard
 */
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('home'));
});


/**
 * Auth
 */
Breadcrumbs::register('login', function($breadcrumbs)
{
    $breadcrumbs->push('Login', route('login'));
});
Breadcrumbs::register('register', function($breadcrumbs)
{
    $breadcrumbs->push('Register', route('register'));
});
Breadcrumbs::register('password.email', function($breadcrumbs)
{
    $breadcrumbs->push('Password Reset', route('password.email'));
});
Breadcrumbs::register('password.reset', function($breadcrumbs, $token)
{
    $breadcrumbs->push('Password Reset', route('password.reset', $token));
});


/**
 * Range Trips
 */
// Home > Range Trips
Breadcrumbs::register('trips', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Range Trips', route('trips.index'));
});
// Home > Range Trips > [Trip]
Breadcrumbs::register('trips.show', function($breadcrumbs, $trip)
{
    $breadcrumbs->parent('trips');
    $breadcrumbs->push($trip->trip_date->toDateString(), route('trips.show', $trip->id));
});
// Home > Range Trips > [Create]
Breadcrumbs::register('trips.create', function($breadcrumbs)
{
    $breadcrumbs->parent('trips');
    $breadcrumbs->push('Create', route('trips.create'));
});
// Home > Range Trips > Trip > [Edit]
Breadcrumbs::register('trips.edit', function($breadcrumbs, $trip)
{
    $breadcrumbs->parent('trip', $trip);
    $breadcrumbs->push('Edit '.$trip->trip_date->toDateString(), route('trips.edit', $trip->id));
});

/**
 * Shoots
 */
// Home > Range Trips > Trip > [Shoot]
Breadcrumbs::register('shoots', function($breadcrumbs, $shoot)
{
    $breadcrumbs->parent('trips.show', $shoot->trip);
    $breadcrumbs->push($shoot->id, route('trips.shoots.show', [$shoot->trip->id, $shoot->id]));
});
// Home > Range Trips > Trip > Shoot > [Edit]
Breadcrumbs::register('shoots.edit', function($breadcrumbs, $shoot)
{
    $breadcrumbs->parent('shoot', $shoot);
    $breadcrumbs->push('Edit '.$shoot->id, route('trips.shoots.show', [$shoot->trip->id, $shoot->id]));
});
// Home > Range Trips > Trip > Shoot > [Create]
Breadcrumbs::register('shoots.create', function($breadcrumbs, $trip)
{
    $breadcrumbs->parent('trips.show', $trip);
    $breadcrumbs->push('Create', route('trips.shoots.create', $trip->id));
});


/**
 * Cartridges
 **/
// Home > Cartridges
Breadcrumbs::register('cartridges', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Cartridges', route('cartridges.index'));
});
// Home > Cartridges > Create
Breadcrumbs::register('cartridges.create', function($breadcrumbs)
{
    $breadcrumbs->parent('cartridges');
    $breadcrumbs->push('New', route('cartridges.create'));
});
// Home > Cartridges > Cartridge
Breadcrumbs::register('cartridges.show', function($breadcrumbs, $cartridge)
{
    $breadcrumbs->parent('cartridges');
    $breadcrumbs->push($cartridge->size, route('cartridges.show', $cartridge));
});
// Home > Cartridges > Cartridge > Edit
Breadcrumbs::register('cartridges.edit', function($breadcrumbs, $cartridge)
{
    $breadcrumbs->parent('cartridges.show');
    $breadcrumbs->push('Edit '.$cartridge->size, route('cartridges.edit', $cartridge));
});


/**
 * Bullets
 **/
// Home > Bullets
Breadcrumbs::register('bullets', function($breadcrumbs, $cartridge)
{
    $breadcrumbs->parent('cartridges');
    $breadcrumbs->push($cartridge->size . ' Bullets', route('cartridges.bullets.index', $cartridge->id));
});
// Home > Bullets > Create
Breadcrumbs::register('bullets.create', function($breadcrumbs, $cartridge)
{
    $breadcrumbs->parent('bullets', $cartridge);
    $breadcrumbs->push('New', route('cartridges.create'));
});
// Home > Bullets > Bullet
Breadcrumbs::register('bullet', function($breadcrumbs, $bullet)
{
    $breadcrumbs->parent('bullets', $bullet->cartridge);
    $breadcrumbs->push($bullet->getLabel('short'), route('cartridges.bullets.show', [$bullet->cartridge->id, $bullet->id]));
});
// Home > Bullets > Bullet
Breadcrumbs::register('bullet.edit', function($breadcrumbs, $bullet)
{
    $breadcrumbs->parent('bullet', $bullet);
    $breadcrumbs->push('Edit', route('cartridges.bullets.edit', [$bullet->cartridge->id, $bullet->id]));
});


/**
 * Magazines
 **/
// Home > Magazines
Breadcrumbs::register('magazines', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Magazines', route('magazines.index'));
});

// Home > Magazines > Create
Breadcrumbs::register('magazines.create', function($breadcrumbs)
{
    $breadcrumbs->parent('magazines');
    $breadcrumbs->push('New', route('magazines.create'));
});

// Home > Magazines > Magazine
Breadcrumbs::register('magazines.show', function($breadcrumbs, $magazine)
{
    $breadcrumbs->parent('magazines');
    $breadcrumbs->push($magazine->label, route('magazines.show', $magazine->id));
});


/**
 * Targets
 **/
// Home > Target
Breadcrumbs::register('targets', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Targets', route('targets.index'));
});

// Home > Targets > Create
Breadcrumbs::register('targets.create', function($breadcrumbs)
{
    $breadcrumbs->parent('targets');
    $breadcrumbs->push('New', route('targets.create'));
});

// Home > Targets > Target
Breadcrumbs::register('targets.show', function($breadcrumbs, $target)
{
    $breadcrumbs->parent('targets');
    $breadcrumbs->push($target->label, route('targets.show', $target->id));
});


/**
 * Purposes
 */
// Home > Purposes
Breadcrumbs::register('purposes', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Purposes', route('purposes.index'));
});


/**
 * Ranges
 */
// Home > Ranges
Breadcrumbs::register('ranges', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Ranges', route('ranges.index'));
});

// Home > Ranges > Create
Breadcrumbs::register('ranges.create', function($breadcrumbs)
{
    $breadcrumbs->parent('ranges');
    $breadcrumbs->push('New', route('ranges.create'));
});

// Home > Ranges > Range
Breadcrumbs::register('ranges.show', function($breadcrumbs, $range)
{
    $breadcrumbs->parent('ranges');
    $breadcrumbs->push($range->label, route('ranges.show', $range->id));
});


/**
 * Stores
 */
// Home > Stores
Breadcrumbs::register('stores', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Stores', route('stores.index'));
});
// Home > Stores > Create
Breadcrumbs::register('stores.create', function($breadcrumbs)
{
    $breadcrumbs->parent('stores');
    $breadcrumbs->push('New', route('stores.create'));
});
// Home > Stores > Store
Breadcrumbs::register('stores.show', function($breadcrumbs, $store)
{
    $breadcrumbs->parent('stores');
    $breadcrumbs->push($store->label, route('stores.show', $store->id));
});


/**
 * Orders
 */
// Home > Orders
Breadcrumbs::register('orders', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Orders', route('orders.index'));
});
// Home > Orders > Order
Breadcrumbs::register('orders.show', function($breadcrumbs, $order)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push($order->order_date->toDateString(), route('orders.show', $order->id));
});
// Home > Orders > [Create]
Breadcrumbs::register('orders.create', function($breadcrumbs)
{
    $breadcrumbs->parent('orders');
    $breadcrumbs->push('New', route('orders.create'));
});
// Home > Orders > Order > [Edit]
Breadcrumbs::register('orders.edit', function($breadcrumbs, $order)
{
    $breadcrumbs->parent('orders.show', $order);
    $breadcrumbs->push('Edit '.$order->id, route('orders.edit', $order->id));
});


/**
 * Inventories
 */
// Home > Orders > Order > Inventory
Breadcrumbs::register('inventory', function($breadcrumbs, $inventory)
{
    $breadcrumbs->parent('order', $inventory->order);
    $breadcrumbs->push($inventory->id, route('orders.inventories.show', [$inventory->order->id, $inventory->id]));
});
// Home > Orders > Order > Inventory > [Edit]
Breadcrumbs::register('inventoryEdit', function($breadcrumbs, $inventory)
{
    $breadcrumbs->parent('inventory', $inventory);
    $breadcrumbs->push('Edit '.$inventory->id, route('orders.inventories.edit', [$inventory->order->id, $inventory->id]));
});
// Home > Orders > Order > Inventory > [Create]
Breadcrumbs::register('inventoryCreate', function($breadcrumbs, $order)
{
    $breadcrumbs->parent('order', $order);
    $breadcrumbs->push('Create', route('orders.inventories.create', $order->id));
});


/**
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
