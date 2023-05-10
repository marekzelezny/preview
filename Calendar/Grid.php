<?php

namespace App\Http\Controllers\Components\Calendar\Grid;

use App\Http\Controllers\Components\Calendar\EventRepository;
use App\Models\Event;
use Bootstrap\TimberCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Grid
{
    /**
     * Current date
     */
    public Carbon $date;

    /**
     * Rowspan of rows based on hours and intervals
     */
    public array $rowSpan = [];

    /**
     * Rows to render
     */
    public RowIterator $rows;

    /**
     * Columns to render
     */
    public Collection $columns;

    /**
     * Events matching the current date
     */
    public Collection $events;

    /**
     * Events which have order set to 2
     */
    public Collection $mainEvents;

    public function __construct(string $date)
    {
        $this->date = Carbon::parse($date);

        $this->events = EventRepository::getEvents($date)
            ->groupByAuditorium()
            ->sortByDate()
            ->get();

        $this->mainEvents = EventRepository::getEvents($date)
            ->getMainEvents()
            ->groupByAuditorium()
            ->get();

        $this->rowSpan = [
            'start' => config('calendar.grid.start'),
            'end' => config('calendar.grid.end'),
            'interval' => config('calendar.grid.interval'),
        ];

        $this->rows = new RowIterator(
            startTime: $this->date->copy()->setTime($this->rowSpan['start'], 0),
            endTime: $this->date->copy()->setTime($this->rowSpan['end'], 0),
            interval: $this->rowSpan['interval'],
        );

        $this->columns = TimberCollection::terms([
            'taxonomy' => 'auditorium',
            'hide_empty' => false,
            'meta_query' => [
                [
                    'key' => 'visibility',
                    'compare' => '=',
                    'value' => '1',
                ],
            ],
        ]);
    }

    public static function init(string $date)
    {
        return new static($date);
    }

    /**
     * This formula converts the time interval to a fraction of an hour (5 / 60),
     * and then calculates the number of rows spanned between the starting and ending times.
     */
    public function calculateRowSpans()
    {
        return ($this->rowSpan['end'] - $this->rowSpan['start']) / ($this->rowSpan['interval'] / 60);
    }

    /**
     * Returns event matching column (auditorium) and time
     */
    public function currentEvent(string $auditorium, Carbon $time): ?Event
    {
        if (! $this->events->has($auditorium)) {
            return null;
        }

        $events = $this->events[$auditorium]->filter(function ($event) use ($time) {
            return $event->date['start'] == $time && $event->isMainEvent() == false;
        });

        return $events->first();
    }

    /**
     * Returns main event matching date
     */
    public function getMainEvent(string $auditorium, Carbon $start, Carbon $end): ?Event
    {
        if (! $this->mainEvents->has($auditorium)) {
            return null;
        }

        return $this->mainEvents[$auditorium]->filter(function ($event) use ($start, $end) {
            return $event->date['start']->lessThanOrEqualTo($start) && $event->date['end']->greaterThanOrEqualTo($end);
        })->first();
    }

    /**
     * Detects if there is an event in given time
     * Used to skip columns with rowspan
     */
    public function hasEventInThisTime(string $auditorium, Carbon $time): bool
    {
        if (! $this->events->has($auditorium)) {
            return false;
        }

        $events = $this->events[$auditorium]->filter(function ($event) use ($time) {
            return $event->isMainEvent() == false
                && $event->date['start']->lessThanOrEqualTo($time)
                && $event->date['end']->greaterThan($time);
        });

        if ($events->count() > 0) {
            return true;
        }

        return false;
    }
}
