<?php

namespace App\Extensions\PeriodikAbo\Traits;

use Illuminate\Http\Client\Response;

trait HasCountry
{
    public static function countries(): Response
    {
        return self::init()->get('address/country');
    }

    public static function subscriberDetail(string $email): Response
    {
        return self::init()->get('address/detail', [
            'email' => $email,
        ]);
    }
}
