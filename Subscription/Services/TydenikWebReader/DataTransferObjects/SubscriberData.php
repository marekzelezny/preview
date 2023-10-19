<?php

namespace Services\TydenikWebReader\DataTransferObjects;

use Spatie\LaravelData\Data;

class SubscriberData extends Data
{
    public function __construct(
        public int $id,
        public string $full_name,
        public string $email,
        public SubscriptionData $subscription,
    ) {}
}
