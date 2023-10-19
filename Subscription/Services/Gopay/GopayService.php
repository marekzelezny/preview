<?php

namespace Services\Gopay;

use GoPay\Payments;

class GopayService
{
    protected Payments $gopay;

    protected array $config;

    protected array $services;

    protected bool $needReInit = false;

    public function __construct(
        string $goid,
        string $clientId,
        string $clientSecret,
        string $language,
        bool $isProductionMode
    )
    {
        $urls = [
            true => 'https://gate.gopay.cz/api',
            false => 'https://gw.sandbox.gopay.com/api'
        ];

        $this->config = [
            'goid' => $goid,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'language' => $language,
            'gatewayUrl' => $urls[$isProductionMode],
        ];

        $this->initGoPay();
    }

    protected function initGoPay(): Payments
    {
        $this->services['cache'] = new GopayTokenCache();
        $this->gopay = GopayPayments::init($this->config, $this->services);

        if ($this->needReInit) {
            $this->needReInit = true;
        }

        return $this->gopay;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        } elseif (method_exists($this->gopay, $name)) {
            if ($this->needReInit) {
                $gp = $this->initGoPay();
            } else {
                $gp = $this->gopay;
            }

            return $gp->{$name}(...$arguments);
        }

        return null;
    }
}
