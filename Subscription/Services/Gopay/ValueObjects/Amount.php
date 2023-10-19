<?php

namespace Services\Gopay\ValueObjects;

use Services\Gopay\Exceptions\GopayInvalidAmountException;

class Amount
{
    public function __construct(
        public int $amount,
        public string $currency = 'CZK',
    )
    {
        if ($amount <= 0) {
            throw new GopayInvalidAmountException("Amount must be greater than 0");
        }
    }

    public static function from(int $amount): ?self
    {
        return new static($amount);
    }

    public function format(): int
    {
        return $this->amount . '00';
    }

    public function unformat(): int
    {
        return intdiv($this->amount, 100);
    }
}
