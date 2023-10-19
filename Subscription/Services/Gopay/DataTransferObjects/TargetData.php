<?php

namespace Services\Gopay\DataTransferObjects;

use Spatie\LaravelData\Data;

class TargetData extends Data
{
    public function __construct(
        public readonly string $type = 'ACCOUNT',
        public string $goid = '0',
    )
    {
        // Sets goid automatically by config data
        $this->goid = config('services.gopay.goid');
    }
}
