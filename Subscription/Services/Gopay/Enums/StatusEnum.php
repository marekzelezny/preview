<?php

namespace Services\Gopay\Enums;

enum StatusEnum: string
{
    case CREATED = 'CREATED';
    case PAID = 'PAID';
    case CANCELED = 'CANCELED';
    case REFUNDED = 'REFUNDED';
    case PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    case PAYMENT_METHOD_CHOSEN = 'PAYMENT_METHOD_CHOSEN';
    case TIMEOUTED = 'TIMEOUTED';
    case AUTHORIZED = 'AUTHORIZED';
    case REQUESTED = 'REQUESTED';
    case STARTED = 'STARTED';
    case STOPPED = 'STOPPED';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Nová platba',
            self::PAID => 'Zaplaceno',
            self::CANCELED => 'Zrušeno',
            self::REFUNDED => 'Vráceno',
            self::PARTIALLY_REFUNDED => 'Částečně vráceno',
            self::PAYMENT_METHOD_CHOSEN => 'Platební metoda potvrzena',
            self::TIMEOUTED => 'Platbě vypršela životnost',
            self::AUTHORIZED => 'Platba předautorizována',
            self::REQUESTED => 'Opakovaní platby vytvořeno, čeká se na autorizaci iniciační platby',
            self::STARTED => 'Opakování platby aktivní',
            self::STOPPED => 'Opakování platby zrušeno',
        };
    }
}
