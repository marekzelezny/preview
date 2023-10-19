<?php

namespace Services\TydenikWebReader\DataTransferObjects;

use Carbon\Carbon;
use Services\TydenikWebReader\Enums\SubscriptionWpTermEnum;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class SubscriptionUpdateData extends Data
{
    public function __construct(
        public string $email,

        public SubscriptionWpTermEnum $subscription_type,

        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Ymd')]
        public Carbon $valid_from,

        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Ymd')]
        public Carbon $valid_to,

        public ?bool $has_recurring_payment,
        public ?int $gopay_id,
    ) {}
}
