<?php

namespace App\Extensions\PeriodikAbo\Traits;

use App\Enums\SubscriptionType;
use Illuminate\Http\Client\Response;

trait HasPrice
{
    public static function prices(SubscriptionType $subscriptionType = null): Response
    {
        return self::init()->get('price/list', [
            'productId' => $subscriptionType?->id(),
        ]);
    }

    public static function price(int $priceId): Response
    {
        return self::init()->get('price/detail', [
            'id' => $priceId,
        ]);
    }
}
