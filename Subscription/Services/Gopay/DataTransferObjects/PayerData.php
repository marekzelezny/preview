<?php

namespace Services\Gopay\DataTransferObjects;

use Services\Gopay\Enums\PaymentMethodEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PayerData extends Data
{
    public function __construct(
        public ContactData $contact,
        public PaymentMethodEnum|Optional $default_payment_instrument,
    )
    {}
}
