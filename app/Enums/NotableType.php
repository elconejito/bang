<?php

namespace App\Enums;

use App\Models\Ammunition;
use App\Models\Firearm;
use App\Models\Light;
use App\Models\Location;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Order;
use App\Models\Range;
use App\Models\SessionLine;
use App\Models\Store;
use App\Models\Suppressor;
use App\Models\TrainingSession;
use Illuminate\Database\Eloquent\Model;

enum NotableType: string
{
    case Ammunition = 'ammunition';
    case Firearm = 'firearms';
    case Light = 'lights';
    case Location = 'locations';
    case Magazine = 'magazines';
    case MiscAccessory = 'misc-accessories';
    case Optic = 'optics';
    case Order = 'orders';
    case Range = 'ranges';
    case SessionLine = 'session-lines';
    case Store = 'stores';
    case Suppressor = 'suppressors';
    case TrainingSession = 'training';

    /**
     * @return class-string<Model>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::Ammunition => Ammunition::class,
            self::Firearm => Firearm::class,
            self::Light => Light::class,
            self::Location => Location::class,
            self::Magazine => Magazine::class,
            self::MiscAccessory => MiscAccessory::class,
            self::Optic => Optic::class,
            self::Order => Order::class,
            self::Range => Range::class,
            self::SessionLine => SessionLine::class,
            self::Store => Store::class,
            self::Suppressor => Suppressor::class,
            self::TrainingSession => TrainingSession::class,
        };
    }

    public static function routePattern(): string
    {
        return '(?:'.collect(self::cases())->pluck('value')->implode('|').')';
    }
}
