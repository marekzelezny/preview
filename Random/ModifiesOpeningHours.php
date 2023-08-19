<?php

namespace App\Concerns;

trait ModifiesOpeningHours
{
    /**
     * Creates output for twig
     */
    public function processOpeningHours($data): array
    {
        $r = [
            'hasAfternoonTime' => false,
            'openingHour' => null,
            'closingHour' => null,
        ];

        /**
         * Get current day in format 'mon', 'tue', etc.. for array
         */
        $currentDay = new \DateTime();
        $currentDay = strtolower($currentDay->format('D'));
        $day = $data[$currentDay];

        /**
         * Create opening and closing hour
         */
        if ($day['morning'] != null) {
            $r['openingHour'] = \DateTime::createFromFormat('G:i', $day['morning']['from']);
            $r['closingHour'] = \DateTime::createFromFormat('G:i', $day['morning']['to']);
        }

        /**
         * If afternoon times, then compare for opening and closing hour
         */
        if ($day['afternoon'] != null) {
            $afternoonOpeningHour = \DateTime::createFromFormat('G:i', $day['afternoon']['from']);
            $afternoonClosingHour = \DateTime::createFromFormat('G:i', $day['afternoon']['to']);

            $r['openingHour'] = $afternoonOpeningHour < $r['openingHour'] ? $afternoonOpeningHour : $r['openingHour'];
            $r['closingHour'] = $afternoonClosingHour > $r['closingHour'] ? $afternoonClosingHour : $r['closingHour'];
        }

        /**
         * Convert times to only hours
         */
        if ($r['openingHour'] && $r['closingHour']) {
            $r['openingHour'] = $r['openingHour']->format('G:i');
            $r['closingHour'] = $r['closingHour']->format('G:i');
        }

        /**
         * If there is any day with afternoon times
         */
        foreach ($data as $day) {
            if ($day['afternoon']) {
                $r['hasAfternoonTime'] = true;
            }
        }

        return [
            'info' => $r,
            'data' => $data,
        ];
    }

    /**
     * Modifies the ACF data to be compatible with API and the processOpeningHours method
     */
    public function modifyAcfData(array $openingHours): array
    {
        foreach($openingHours as $key => $opening) {
            if(empty($opening['morning']['from']) && empty($opening['morning']['to'])) {
                $openingHours[$key]['morning'] = null;
            }

            if(empty($opening['afternoon']['from']) && empty($opening['afternoon']['to'])) {
                $openingHours[$key]['afternoon'] = null;
            }
        }

        return $openingHours;
    }
}
