<?php

namespace App\Extensions\CareCloudApi\Concerns;

use DateTime;

trait HasOpeningHours
{
    public function mapOpeningHours(array $hours): array
    {
        return [
            'mon' => $this->setOpeningPerDay($hours[0]),
            'tue' => $this->setOpeningPerDay($hours[1]),
            'wed' => $this->setOpeningPerDay($hours[2]),
            'thu' => $this->setOpeningPerDay($hours[3]),
            'fri' => $this->setOpeningPerDay($hours[4]),
            'sat' => $this->setOpeningPerDay($hours[5]),
            'sun' => $this->setOpeningPerDay($hours[6]),
        ];
    }

    protected function setOpeningPerDay(object $day): array
    {
        $a = [
            'morning' => null,
            'afternoon' => null
        ];

        if (!is_null($day->time_intervals[0]->from) && !is_null($day->time_intervals[0]->to)) {
            $a['morning'] = [
                'from' => $this->formatHour($day->time_intervals[0]->from),
                'to'   => $this->formatHour($day->time_intervals[0]->to),
            ];
        }

        if (!is_null($day->time_intervals[1]->from) && !is_null($day->time_intervals[1]->to)) {
            $a['afternoon'] = [
                'from' => $this->formatHour($day->time_intervals[1]->from),
                'to'   => $this->formatHour($day->time_intervals[1]->to),
            ];
        }
        return $a;
    }
}
