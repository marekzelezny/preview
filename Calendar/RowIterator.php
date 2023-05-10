<?php

namespace App\Http\Controllers\Components\Calendar\Grid;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Iterator;

class RowIterator implements Iterator
{
    private Carbon $startTime;

    private Carbon $endTime;

    private Carbon $currentTime;

    private CarbonInterval $interval;

    public function __construct($startTime, $endTime, $interval)
    {
        $this->startTime = Carbon::parse($startTime);
        $this->endTime = Carbon::parse($endTime);
        $this->interval = CarbonInterval::minutes($interval);
        $this->currentTime = $startTime->copy();
    }

    public function current(): Carbon
    {
        return $this->currentTime;
    }

    public function key(): mixed
    {
        return null;
    }

    public function next(): void
    {
        $this->currentTime->add($this->interval);
    }

    public function rewind(): void
    {
        $this->currentTime = $this->startTime;
    }

    public function valid(): bool
    {
        return $this->currentTime->lessThanOrEqualTo($this->endTime);
    }
}
