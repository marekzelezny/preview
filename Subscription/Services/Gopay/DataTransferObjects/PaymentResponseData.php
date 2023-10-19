<?php

namespace Services\Gopay\DataTransferObjects;

use Services\Gopay\DataTransferObjects\Casts\AmountCast;
use Services\Gopay\Enums\PaymentMethodEnum;
use Services\Gopay\Enums\StatusEnum;
use Services\Gopay\ValueObjects\Amount;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PaymentResponseData extends Data
{
    public readonly TargetData $target;
    public readonly string $currency;

    public function __construct(
        public readonly int $id,
        public readonly int|Optional $parent_id,
        #[WithCast(AmountCast::class)]
        public readonly int $amount,
        public readonly string $order_number,
        public readonly StatusEnum|Optional $state,
        public readonly PaymentMethodEnum|Optional $payment_instrument,
        public readonly PayerData $payer,
        public readonly RecurrenceData|Optional $recurrence,
        public readonly string|Optional $gw_url,
        public readonly string|Optional $lang,
    )
    {
        $this->target = new TargetData();
        $this->currency = 'CZK';
    }

    public function hasRecurrenceActive(): bool
    {
        return is_a($this->recurrence, RecurrenceData::class) && $this->recurrence->isActive();
    }
}
