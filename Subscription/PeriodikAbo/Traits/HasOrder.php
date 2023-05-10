<?php

namespace App\Extensions\PeriodikAbo\Traits;

use Illuminate\Http\Client\Response;

trait HasOrder
{
    public static function createOrder(array $orderDetails): Response
    {
        return self::init()->get('order/create', $orderDetails);
    }

    public static function confirmPayment(string $variableSymbol): Response
    {
        return self::init()->get('order/paymentConfirm', [
            'varsymbol' => $variableSymbol,
        ]);
    }

    public static function paymentList(): Response
    {
        return self::init()->get('order/paymentList');
    }

    public static function orderCreateList(): Response
    {
        return self::init()->get('order/createList');
    }

    public static function getOrdersBySubscriber(string $email): Response
    {
        return self::init()->get('order/list', [
            'email' => $email,
        ]);
    }

    public static function getOrder(int $orderId): Response
    {
        return self::init()->get('order/detail', [
            'id' => $orderId,
        ]);
    }

    public static function getVariableSymbol(): int
    {
        return (int) self::init()->get('order/varsymbol')->json();
    }

    public static function stopRecurrentPayment(int $orderId): Response
    {
        return self::init()->get('order/stopRecurrent', [
            'id' => $orderId,
        ]);
    }
}
