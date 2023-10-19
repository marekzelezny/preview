<?php

namespace Services\TydenikWebReader;

use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use Services\TydenikWebReader\DataTransferObjects\SubscriberData;
use Services\TydenikWebReader\DataTransferObjects\SubscriptionUpdateData;

class TydenikWebReader
{
    public function __construct(
        protected string $password,
        protected string $base_url,
    )
    {}

    public function getSubscriber(string $email): SubscriberData|PromiseInterface|Response
    {
        $request = $this->makeRequest()
            ->get(
                url: 'subscription-info',
                query: [
                    'email' => $email
                ]
            );

        if($request->ok()) {
            return SubscriberData::from($request->json());
        }

        return $request;
    }

    public function updateSubscription(SubscriptionUpdateData $data): PromiseInterface|Response
    {
        return $this->makeRequest()
            ->get(
                url: 'subscription-update',
                query: $data->toArray()
            );
    }

    protected function makeRequest(): PendingRequest
    {
        return Http::baseUrl($this->base_url)
            ->withMiddleware(
                Middleware::mapRequest(function (RequestInterface $request) {
                    return $this->withCredentialsQueryParams($request);
                })
            );
    }

    protected function withCredentialsQueryParams(RequestInterface $request): RequestInterface
    {
        return $request->withUri(
            Uri::withQueryValues($request->getUri(), [
                'password' => $this->password,
            ])
        );
    }
}
