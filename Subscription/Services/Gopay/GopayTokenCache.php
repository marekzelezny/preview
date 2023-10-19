<?php

namespace Services\Gopay;

use GoPay\Token\AccessToken;
use GoPay\Token\TokenCache;
use Illuminate\Support\Facades\Cache;

class GopayTokenCache implements TokenCache
{
    public function setAccessToken($client, AccessToken $t): void
    {
        Cache::put("gopay_token_{$client}", serialize($t), config('services.gopay.timeout'));
    }

    public function getAccessToken($client)
    {
        $token = Cache::get("gopay_token_{$client}");
        if (! is_null($token)) {
            return unserialize($token);
        }

        return null;
    }
}
