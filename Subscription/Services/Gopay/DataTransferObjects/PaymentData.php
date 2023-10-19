<?php

namespace Services\Gopay\DataTransferObjects;

use Services\Gopay\DataTransferObjects\Casts\AmountCast;
use Services\Gopay\Enums\PaymentMethodEnum;
use Services\Gopay\Enums\StatusEnum;
use Services\Gopay\ValueObjects\Amount;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PaymentData extends Data
{
    public readonly TargetData $target;
    public readonly string $currency;

    public function __construct(
        #[WithCast(AmountCast::class)]
        public int $amount,
        public string $order_number,
        public PayerData $payer,
        public ?CallbackData $callback,
        public RecurrenceData|Optional $recurrence,
        public readonly PaymentMethodEnum|Optional $payment_method,
    )
    {
        $this->target = new TargetData();
        $this->currency = 'CZK';
    }
}
