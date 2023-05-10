<?php

namespace App\Extensions\PeriodikAbo\Traits;

use Illuminate\Http\Client\Response;

trait HasInvoice
{
    public static function getInvoicesBySubscriber(string $email): Response
    {
        return self::init()->get('invoice/list', [
            'email' => $email,
        ]);
    }

    public static function getInvoice(int $invoiceId): Response
    {
        return self::init()->get('invoice/detail', [
            'id' => $invoiceId,
        ]);
    }

    public static function getInvoicePDF(int $invoiceId): Response
    {
        return self::init()->get('invoice/print', [
            'id' => $invoiceId,
        ]);
    }
}
