<?php

namespace Services\Gopay\Enums;

enum RecurrenceCycleEnum: string
{
    case DAY = 'DAY';
    case WEEK = 'WEEK';
    case MONTH = 'MONTH';
    case ON_DEMAND = 'ON_DEMAND';
}
