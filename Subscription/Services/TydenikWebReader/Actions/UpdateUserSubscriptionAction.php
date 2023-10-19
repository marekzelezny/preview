<?php

namespace Services\TydenikWebReader\Actions;

use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;
use Services\TydenikWebReader\DataTransferObjects\SubscriptionUpdateData;
use Services\TydenikWebReader\TydenikWebReader;

class UpdateUserSubscriptionAction
{
    use AsAction;

    public function __construct(
        public TydenikWebReader $tydenikWebReader,
    )
    {}

    public function handle(
        string $email,
        Carbon $valid_from,
        Carbon $valid_to,
        bool $has_recurring_payment,
        int $gopay_id
    ): void
    {
        $this->tydenikWebReader->updateSubscription(
            SubscriptionUpdateData::from(func_get_args())
        );
    }
}
