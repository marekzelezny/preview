<?php

namespace App\Http\Controllers\Templates;

use App\Http\Controllers\Components\Calendar\CurrentEvents;
use App\Http\Controllers\Components\Calendar\Grid\Grid;
use Bootstrap\Controllers\PageTemplateController;
use Bootstrap\TimberCollection;
use Bootstrap\Traits\HasAcfFields;
use Extended\ACF\Fields\DatePicker;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Location;
use Illuminate\Support\Collection;

class CalendarController extends PageTemplateController
{
    use HasAcfFields;

    /**
     * Template name
     */
    public string $name = 'calendar';

    /**
     * Template title
     */
    public string $title = 'Kalendář';

    /**
     * Twig view path
     */
    public string $path = 'templates/calendar.twig';

    /**
     * Calendar views available to user
     */
    public array $calendarViews = [
        'list' => 'Seznam',
        'grid' => 'Sloupce',
    ];

    public Collection $days;

    /**
     * ACF fields
     */
    public function setFields(): array
    {
        return [
            'title' => ' ',
            'key' => $this->name.'_settings',
            'fields' => [
                Group::make('Nastavení kalendáře', 'calendar')
                    ->fields([
                        Select::make('Výchozí zobrazení kalendáře', 'defaultView')
                            ->choices($this->calendarViews)
                            ->defaultValue('grid'),

                        Repeater::make('Zobrazované dny', 'days')
                            ->instructions('Vyberte dny, které se mají zobrazovat v kalendáři')
                            ->fields([
                                DatePicker::make('Datum', 'date')
                                    ->displayFormat('d.m.Y')
                                    ->returnFormat('Y-m-d'),
                            ])
                            ->min(1)
                            ->layout('table')
                            ->required(),
                    ]),
            ],
            'location' => [
                Location::where('page_template', $this->getFilePath()),
            ],
            'style' => 'default',
            'position' => 'acf_after_title',
            'instruction_placement' => 'label',
            'hide_on_screen' => [
                'permalink',
                'the_content',
                'excerpt',
                'discussion',
                'comments',
                'revisions',
                'author',
                'format',
                'featured_image',
                'categories',
                'tags',
                'send-trackbacks',
            ],
        ];
    }

    public function setContext(): array
    {
        /**
         * Sets selectable days on page
         */
        $this->days = collect(get_field('calendar_days', $this->postId))
            ->pluck('date');

        return [
            'calendar' => $this->setCalendarData(),

            'currentEvents' => CurrentEvents::init(),

            'organizations' => TimberCollection::terms([
                'taxonomy' => 'organization',
                'hide_empty' => true,
                'orderby' => 'name',
            ]),

            'auditoriums' => TimberCollection::terms([
                'taxonomy' => 'auditorium',
                'hide_empty' => false,
                'meta_key' => 'visibility',
                'meta_value' => '0',
                'meta_compare' => '!=',
            ]),
        ];
    }

    public function setCalendarData()
    {
        return [
            'views' => $this->setCalendarViews(),

            'days' => $this->setCalendarDays(),

            'grid' => Grid::init($this->getCalendarCurrentDate()),
        ];
    }

    public function setCalendarViews()
    {
        return [
            'options' => array_keys($this->calendarViews),
            'current' => $_GET['zobrazeni'] ?? get_field('calendar_defaultView', $this->postId),
        ];
    }

    public function setCalendarDays()
    {
        return [
            'options' => $this->days,
            'current' => $this->getCalendarCurrentDate(),
        ];
    }

    public function getCalendarCurrentDate()
    {
        return $_GET['den'] ?? $this->days->first()->format('Y-m-d');
    }
}
