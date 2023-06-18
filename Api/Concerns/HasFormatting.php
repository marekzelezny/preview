<?php

namespace App\Extensions\CareCloudApi\Concerns;

use DateTime;

trait HasFormatting
{
    protected function formatAddress($item): ?object
    {
        if(! $item) {
            return null;
        }

        return (object) [
            'street' => implode(' ', [
                $item->address1,
                $item->address2,
                $item->address3,
                $item->address4,
                $item->address5,
                $item->address6,
                $item->address7,
            ]),
            'city' => $item->city,
            'zip'  => $item->zip,
        ];
    }

    protected function formatPhoneNumber($item): ?string
    {
        if (!is_string($item)) {
            return null;
        }

        return "+{$item}";
    }

    protected function formatHour($item): ?string
    {
        if (!is_string($item)) {
            return null;
        }

        $date = DateTime::createFromFormat('H:i:s', $item);

        return $date->format('H:i');
    }
}
