<?php

namespace Adity\Enums;

enum TagEnum: string
{
    case NEW = 'new';
    case MODIFIED = 'modified';

    public function label() : string
    {
        return match ($this) {
            self::NEW => __('New', 'adity'),
            self::MODIFIED => __('Modified', 'adity'),
        };
    }

    public static function array() : array
    {
        return [
            self::NEW ->name => self::NEW ->label(),
            self::MODIFIED->name => self::MODIFIED->label(),
        ];
    }
}
