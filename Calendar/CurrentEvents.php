<?php

namespace App\Http\Controllers\Components\Calendar;

use Illuminate\Support\Collection;

class CurrentEvents
{
    public Collection $events;

    public function __construct()
    {
        $this->events = EventRepository::getCurrentEvents()
            ->groupByAuditorium()
            ->sortByDate()
            ->get();
    }
}
