<?php

namespace App\Extensions\PeriodikAbo;

use App\Extensions\PeriodikAbo\Traits\HasCountry;
use App\Extensions\PeriodikAbo\Traits\HasInvoice;
use App\Extensions\PeriodikAbo\Traits\HasOrder;
use App\Extensions\PeriodikAbo\Traits\HasPrice;
use App\Extensions\PeriodikAbo\Traits\HasProduct;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;

class PeriodikAbo
{
    use HasProduct, HasPrice, HasCountry, HasOrder, HasInvoice;

    public static function init(): PendingRequest
    {
        return Http::baseUrl(config('services.periodikAbo.baseUrl'))
            ->withMiddleware(
                Middleware::mapRequest(function (RequestInterface $request) {
                    return self::withCredentialsQueryParams($request);
                })
            )
            ->withMiddleware(
                Middleware::mapResponse(function (Response $response) {
                    return self::handleCustomApiExceptions($response);
                })
            );
    }

    public static function withCredentialsQueryParams(RequestInterface $request): RequestInterface
    {
        return $request->withUri(
            Uri::withQueryValues($request->getUri(), [
                'name' => config('services.periodikAbo.username'),
                'password' => config('services.periodikAbo.password'),
            ])
        );
    }

    public static function handleCustomApiExceptions($response): Response
    {
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        $res = json_decode($response->getBody()->getContents(), true);

        if ($res['error'] ?? false) {
            Log::error('PeriodikAbo API Error', [
                'message' => $res['error']['errstr'],
                'response' => $res,
            ]);
        }

        return $response;
    }
}
