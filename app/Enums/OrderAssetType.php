<?php

namespace App\Enums;

use App\Models\Firearm;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Mount;
use App\Models\Optic;
use App\Models\Suppressor;

enum OrderAssetType: string
{
    case Firearm = 'firearm';
    case Suppressor = 'suppressor';
    case Optic = 'optic';
    case Light = 'light';
    case MiscAccessory = 'misc-accessory';
    case Mount = 'mount';
    case Magazine = 'magazine';

    /** @return array<string, class-string> */
    public static function models(): array
    {
        return ['firearm' => Firearm::class, 'suppressor' => Suppressor::class, 'optic' => Optic::class, 'light' => Light::class, 'misc-accessory' => MiscAccessory::class, 'mount' => Mount::class, 'magazine' => Magazine::class];
    }
}
