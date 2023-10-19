<?php

namespace Services\Gopay\DataTransferObjects;

use Services\Gopay\DataTransferObjects\Casts\AmountCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class ItemData extends Data
{
    public function __construct(
        public string $name,
        #[WithCast(AmountCast::class)]
        public int $amount,
        public readonly string $type = 'ITEM',
    )
    {}
}
