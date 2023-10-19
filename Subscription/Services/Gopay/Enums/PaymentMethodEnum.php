<?php

namespace Services\Gopay\Enums;

enum PaymentMethodEnum: string
{
    case PAYMENT_CARD = 'PAYMENT_CARD';
    case RECURRENT_PAYMENT = 'RECURRENT_PAYMENT';
    case BANK_ACCOUNT = 'BANK_ACCOUNT';
    case GPAY = 'GPAY';
    case APPLE_PAY = 'APPLE_PAY';
    case PAYPAL = 'PAYPAL';
    case MPAYMENT = 'MPAYMENT';
    case PRSMS = 'PRSMS';
    case PAYSAFECARD = 'PAYSAFECARD';
    case BITCOIN = 'BITCOIN';
    case CLICK_TO_PAY = 'CLICK_TO_PAY';
}
