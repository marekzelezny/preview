<?php

namespace Services\TydenikWebReader;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TydenikWebReaderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TydenikWebReader::class, function (Application $app) {
            return new TydenikWebReader(
                password: config('services.tydenik_web_reader.password'),
                base_url: config('services.tydenik_web_reader.base_url'),
            );
        });
    }
}
