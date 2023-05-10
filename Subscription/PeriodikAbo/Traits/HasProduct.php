<?php

namespace App\Extensions\PeriodikAbo\Traits;

use App\Enums\SubscriptionType;
use Illuminate\Http\Client\Response;

trait HasProduct
{
    public static function products(): Response
    {
        return self::init()->get('product/list');
    }

    public static function issues(SubscriptionType $subscriptionType, int $all = 1): Response
    {
        return self::init()->get('product/issue', [
            'productId' => $subscriptionType->id(),
            'all' => $all,
        ]);
    }

    public static function terms(SubscriptionType $subscriptionType): Response
    {
        return self::init()->get('product/term', [
            'productId' => $subscriptionType->id(),
        ]);
    }
}
