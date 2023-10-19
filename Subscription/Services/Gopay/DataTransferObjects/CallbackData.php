<?php

namespace Services\Gopay\DataTransferObjects;

use Spatie\LaravelData\Data;

class CallbackData extends Data
{
    public function __construct(
        public string $return_url,
        public string $notification_url,
    )
    {}
}
