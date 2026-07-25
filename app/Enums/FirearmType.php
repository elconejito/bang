<?php

namespace App\Enums;

enum FirearmType: string
{
    case Handgun = 'handgun';
    case Rifle = 'rifle';
    case Shotgun = 'shotgun';

    public function label(): string
    {
        return match ($this) {
            self::Handgun => 'Handgun',
            self::Rifle => 'Rifle',
            self::Shotgun => 'Shotgun',
        };
    }
}
