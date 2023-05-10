<?php

namespace App\Enums;

enum BookingAvailability
{
    case AVAILABLE;
    case UNAVAILABLE;

    public static function tryFromValue(string $value): BookingAvailability
    {
        return match ($value) {
            'AVAILABLE' => self::AVAILABLE,
            'UNAVAILABLE' => self::UNAVAILABLE,
            default => throw new \Exception('Invalid value'),
        };
    }

    public static function tryFromCsv(string $value): self
    {
        return match ($value) {
            'T' => self::AVAILABLE,
            'F' => self::UNAVAILABLE,
            default => null,
        };
    }

    public static function array(): array
    {
        return [
            self::AVAILABLE->name => 'Dostupné',
            self::UNAVAILABLE->name => 'Obsazené',
        ];
    }

    public function name(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Dostupné',
            self::UNAVAILABLE => 'Obsazené',
        };
    }

    public function classColor(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bck-green',
            self::UNAVAILABLE => 'bck-red',
        };
    }
}
