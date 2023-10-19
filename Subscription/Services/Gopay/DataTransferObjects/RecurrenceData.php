<?php

namespace Services\Gopay\DataTransferObjects;

use Carbon\Carbon;
use Services\Gopay\Enums\RecurrenceCycleEnum;
use Services\Gopay\Enums\StatusEnum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class RecurrenceData extends Data
{
    public function __construct(
        #[MapInputName('recurrence_date_to')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date_to,

        #[MapInputName('recurrence_state')]
        public readonly StatusEnum|Optional $state,

        #[MapInputName('recurrence_period')]
        public int $period = 12,

        #[MapInputName('recurrence_cycle')]
        public readonly RecurrenceCycleEnum $cycle = RecurrenceCycleEnum::ON_DEMAND,
    )
    {}

    public function isNotEmpty(): bool
    {
        return property_exists($this, 'state');
    }

    public function isActive(): bool
    {
        return $this->isNotEmpty() && $this->state === StatusEnum::STARTED;
    }
}
