<?php

namespace Services\Gopay\DataTransferObjects\Casts;

use Services\Gopay\ValueObjects\Amount;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class AmountCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): mixed
    {
        $amount = Amount::from($value);

        /**
         * Value will be unformatted from cents if status is in $context (meaning response from GoPay)
         */
        return isset($context['state']) ? $amount->unformat() : $amount->format();
    }
}
