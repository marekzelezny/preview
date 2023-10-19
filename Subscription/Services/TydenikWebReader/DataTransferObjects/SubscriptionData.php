<?php

namespace Services\TydenikWebReader\DataTransferObjects;

use Carbon\Carbon;
use Services\TydenikWebReader\Enums\SubscriptionWpTermEnum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class SubscriptionData extends Data
{
    public function __construct(
        public bool $is_active,

        #[MapInputName('type.id')]
        public SubscriptionWpTermEnum $subscription_type,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Ymd')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Ymd')]
        public Carbon $valid_from,

        #[WithCast(DateTimeInterfaceCast::class, format: 'Ymd')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Ymd')]
        public Carbon $valid_to,

        public array $recurringPayment,
    ) {}
}
