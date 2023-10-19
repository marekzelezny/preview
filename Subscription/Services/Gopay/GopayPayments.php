<?php

namespace Services\Gopay;

use GoPay\GoPay;
use GoPay\Http\Response;
use GoPay\OAuth2;
use GoPay\Payments;
use GoPay\Definition\TokenScope;
use GoPay\Definition\Language;
use GoPay\Http\JsonBrowser;
use GoPay\Http\Log\NullLogger;
use GoPay\Token\CachedOAuth;
use Services\Gopay\DataTransferObjects\PaymentData;
use Services\Gopay\DataTransferObjects\PaymentResponseData;
use Services\Gopay\Exceptions\GopayPaymentException;

class GopayPayments extends Payments
{
    public function createPayment(PaymentData $paymentData)
    {
        return $this->newPayment(
            rawPayment: $paymentData->toArray()
        );
    }

    public function createRecurrence(int $id, PaymentData $paymentData)
    {
        return $this->newRecurrence(
            id: $id,
            payment: $paymentData->toArray()
        );
    }

    public static function init(array $userConfig, array $userServices = []): GopayPayments
    {
        $config = $userConfig + [
            'scope' => TokenScope::ALL,
            'language' => Language::ENGLISH,
            'timeout' => 30
        ];

        $services = $userServices + [
            'cache' => new GopayTokenCache,
            'logger' => new NullLogger
        ];

        $browser = new JsonBrowser($services['logger'], $config['timeout']);
        $gopay = new GoPay($config, $browser);
        $auth = new CachedOAuth(new OAuth2($gopay), $services['cache']);

        return new GopayPayments($gopay, $auth);
    }

    public function get($urlPath): PaymentResponseData
    {
        $payment = parent::get($urlPath);

        if ($payment->hasSucceed()) {
            //return $payment->json;
            return PaymentResponseData::from($payment->json);
        }

        throw GopayPaymentException::fromResponse($payment->json['errors'][0]);
    }

    public function post($urlPath, $contentType, $data = null): PaymentResponseData
    {
        $payment = parent::post($urlPath, $contentType, $data);

        if ($payment->hasSucceed()) {
            return PaymentResponseData::from($payment->json);
        }

        throw GopayPaymentException::fromResponse($payment->json['errors'][0]);
    }

    public function delete($urlPath): array|Response
    {
        $payment = parent::delete($urlPath);

        if ($payment->hasSucceed()) {
            return $payment->json;
        }

        throw GopayPaymentException::fromResponse($payment->json['errors'][0]);
    }
}
