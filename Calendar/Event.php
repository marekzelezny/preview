<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Timber\Term;

class Event
{
    public int $id;

    public int $order;

    public string $title;

    public string $description;

    public ?Term $auditorium;

    public array $date;

    public ?Collection $organization;

    public ?Collection $lecturer;

    public array|bool $files;

    public int $rowspan;

    public bool $exists = true;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->checkIfEventExists();
        $this->setData();
    }

    protected function checkIfEventExists(): void
    {
        if (! get_post($this->id)) {
            $this->exists = false;
        }
    }

    protected function setData(): void
    {
        if (! $this->exists) {
            return;
        }

        $this->title = $this->setTitle($this->id);
        foreach (get_fields($this->id) as $key => $value) {
            $this->{$key} = match ($key) {
                'date' => $this->mapDates($value),
                default => $value,
            };
        }

        if (property_exists($this, 'date')) {
            $this->date = $this->mapDates(get_field('date', $this->id));
        }

        $this->rowspan = $this->calculateRowSpan();
    }

    protected function setTitle(int $id): string
    {
        return get_the_title($id);
    }

    public function isMainEvent(): bool
    {
        return $this->order === 2;
    }

    protected function mapDates($dates): array
    {
        return array_map(function ($date) {
            return Carbon::parse($date)->locale('cs');
        }, $dates);
    }

    protected function calculateRowSpan(): int
    {
        if (! isset($this->date)) {
            return 1;
        }

        $diffInMinutes = $this->date['start']->diffInMinutes($this->date['end']);

        $interval = config('calendar.grid.interval');

        return (int) ceil($diffInMinutes / $interval);
    }

    public function hasNotContent(): bool
    {
        return empty($this->description) && empty($this->files);
    }

    public function getLecturersByOrganization(): ?Collection
    {
        if (! isset($this->lecturer)) {
            return null;
        }

        return collect($this->lecturer)->groupBy(function ($lecturer) {
            return collect($lecturer->terms('organization'))->first()->name;
        });
    }
}
