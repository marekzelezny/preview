<?php

namespace Services\Gopay\DataTransferObjects;

use Services\Gopay\DataTransferObjects\Casts\AmountCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class RecurringPaymentData extends Data
{

    public function __construct(
        #[WithCast(AmountCast::class)]
        public int $amount,
        public string $order_number,
        public string $currency = 'CZK',
    )
    {}
}
