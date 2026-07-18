<?php

namespace App\Enums;

enum ArchiveReason: string
{
    case Sold = 'sold';
    case Transferred = 'transferred';
    case Repair = 'repair';
    case Broken = 'broken';
    case Retired = 'retired';
    case Lost = 'lost';
    case Stolen = 'stolen';
    case Destroyed = 'destroyed';
    case Other = 'other';
}
