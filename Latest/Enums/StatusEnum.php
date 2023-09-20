<?php

namespace Adity\Enums;

enum StatusEnum: string
{
    case ACTIVE = 'active';
    case HISTORICAL = 'historical';
    case ACCEPTED = 'accepted';

    public function label() : string
    {
        return match ($this) {
            self::ACTIVE => __('Active', 'adity'),
            self::HISTORICAL => __('Historical', 'adity'),
            self::ACCEPTED => __('Accepted', 'adity'),
        };
    }

    public static function array() : array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::ACCEPTED->value => self::ACCEPTED->label(),
            self::HISTORICAL->value => self::HISTORICAL->label(),
        ];
    }

    public static function tryFromName(string $name) : ?self
    {
        return match ($name) {
            self::ACTIVE->name => self::ACTIVE,
            self::HISTORICAL->name => self::HISTORICAL,
            self::ACCEPTED->name => self::ACCEPTED,
            default => null,
        };
    }
}
