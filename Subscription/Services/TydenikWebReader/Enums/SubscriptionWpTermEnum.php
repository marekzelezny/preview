<?php

namespace Services\TydenikWebReader\Enums;

enum SubscriptionWpTermEnum: int
{
    case MONTH = 278;
    case QUARTER = 169;
    case HALFYEAR = 5;
    case YEAR = 4;
    case UNLIMITED = 139;

    public function label(): string
    {
        return match ($this) {
            self::MONTH => 'Měsíční',
            self::QUARTER => 'Čtvrtletní',
            self::HALFYEAR => 'Půlroční',
            self::YEAR => 'Roční',
            self::UNLIMITED => 'Neomezené',
        };
    }
}
