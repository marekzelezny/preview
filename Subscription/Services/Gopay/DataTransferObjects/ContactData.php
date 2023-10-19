<?php

namespace Services\Gopay\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ContactData extends Data
{
    public function __construct(
        public string $email,
        public string|Optional $first_name,
        public string|Optional $last_name,
        public string|Optional $phone_number,
        public string|Optional $city,
        public string|Optional $street,
        public string|Optional $postal_code,
        public string $country_code = 'CZE',
    )
    {}
}
