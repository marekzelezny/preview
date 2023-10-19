<?php

namespace Services\Gopay;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GopayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GopayService::class, function (Application $app) {
            return new GopayService(
                goid: config('services.gopay.goid'),
                clientId: config('services.gopay.client_id'),
                clientSecret: config('services.gopay.client_secret'),
                language: config('services.gopay.language'),
                isProductionMode: config('services.gopay.is_production_mode')
            );
        });
    }
}
