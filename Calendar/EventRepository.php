<?php

namespace App\Http\Controllers\Components\Calendar;

use App\Models\Event;
use Illuminate\Support\Collection;

class EventRepository
{
    public string $date;

    public Collection $events;

    public function __construct(string $date, string $dateFormat = 'DATE', bool $allAuditoriums = false)
    {
        $options = [
            'post_type' => 'event',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'date_start_clause' => [
                    'key' => 'date_start',
                    'compare' => '<=',
                    'value' => $date,
                    'type' => $dateFormat,
                ],
                'end_date_clause' => [
                    'key' => 'date_end',
                    'compare' => '>=',
                    'value' => $date,
                    'type' => $dateFormat,
                ],
                'timestamp_clause' => [
                    'key' => 'isss_importer_events_timestamp',
                    'compare' => '=',
                    'value' => getEventImportTimestamp(),
                ],
                'order_clause' => [
                    'key' => 'order',
                    'compare' => 'EXISTS',
                ],
            ],
            'order' => 'ASC',
            'orderby' => [
                'order_clause' => 'DESC',
                'date_start' => 'ASC',
            ],
        ];

        if ($allAuditoriums === false) {
            $options['tax_query'] = [
                [
                    'taxonomy' => 'auditorium',
                    'field' => 'id',
                    'terms' => get_terms([
                        'taxonomy' => 'auditorium',
                        'hide_empty' => false,
                        'fields' => 'ids',
                        'meta_query' => [
                            [
                                'key' => 'visibility',
                                'compare' => '=',
                                'value' => '1',
                            ],
                        ],
                    ]),
                ],
            ];
        }

        $idQuery = get_posts($options);

        $this->events = collect($idQuery)->map(fn ($id) => new Event($id));
        $this->date = $date;

        return $this;
    }

    public static function getEvents(string $date, string $dateFormat = 'DATE'): static
    {
        return new static(...func_get_args());
    }

    public static function getCurrentEvents(): static
    {
        return new static(\getCalendarCurrentDateTime(), 'DATETIME', true);
    }

    public function getWithoutMainEvents(): static
    {
        $this->events = $this->events->filter(fn ($event) => $event->isMainEvent() === false);

        return $this;
    }

    public function getMainEvents(): static
    {
        $this->events = $this->events->filter(fn ($event) => $event->isMainEvent());

        return $this;
    }

    public function sortByOrder(): static
    {
        $this->events = $this->events->sortBy('order');

        return $this;
    }

    public function sortbyDate(): static
    {
        $this->events = $this->events->sortBy('date.start');

        return $this;
    }

    public function groupByAuditorium(): static
    {
        $this->events = $this->events->groupBy('auditorium.name');

        return $this;
    }

    public function sortAuditoriumsBy(array $order): static
    {
        $this->events = $this->events->sortBy(function ($auditorium) use ($order) {
            return array_search($auditorium, $order);
        });

        return $this;
    }

    public function sortAuditoriumsByWpOrder()
    {
        $auditoriums = get_terms([
            'taxonomy' => 'auditorium',
            'hide_empty' => false,
            'fields' => 'names',
            'meta_key' => 'visibility',
            'meta_value' => '0',
            'meta_compare' => '!=',
        ]);

        $this->events = $this->events->sortBy(function ($event) use ($auditoriums) {
            return array_search($event->auditorium->name, $auditoriums);
        });

        return $this;
    }

    public function sortByOrderAndDate(): static
    {
        $this->events = $this->events->sortBy([
            'order',
            'date.start',
        ]);

        return $this;
    }

    public function get(): Collection
    {
        return $this->events;
    }

    public function debug(): static
    {
        $this->events = $this->events->map(function ($group) {
            return $group->map(function ($event) {
                return "{$event->date['start']->format('H:i')} - {$event->date['end']->format('H:i')} - {$event->title}";
            });
        });

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            $this->events = $this->{$name}(...$arguments);
        } elseif (method_exists($this->events, $name)) {
            $this->events = $this->events->{$name}(...$arguments);
        }

        return $this;
    }
}
