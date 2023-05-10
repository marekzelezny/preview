<?php

namespace App\Extensions\Importer\Controllers;

use App\Enums\BookingAvailability;
use App\Enums\BookingVisibility;
use App\Extensions\Importer\Models\Booking;

class BookingImporter extends BaseController
{
    public string $table = 'isss_importer_bookings';

    public string $postType = 'booking';

    public function importAll(): string
    {
        $this->deleteExistingWpPosts();
        $this->truncateTable();
        $this->getRecordsFromCsv();

        $this->records->each(function ($row) {
            $this->addToDatabase(
                $this->mapRow($row)
            );
        });

        return "Proběhl import {$this->counter} ubytování.";
    }

    public function addToDatabase(array $row): static
    {
        $model = Booking::create($row);

        if ($model) {
            $model->createWordpressModel();
            $this->counter++;
        }

        return $this;
    }

    public function mapRow(array $row): array
    {
        return [
            'name' => $row['nazev'],
            'visible' => BookingVisibility::tryFromCsv($row['vyber']),
            'available' => BookingAvailability::tryFromCsv($row['volny']),
            'capacity' => $row['kapacita'] === '' ? null : (int) $row['kapacita'],
            'stars' => $row['uroven'],
            'address' => [
                'street' => $row['ulice'].' '.$row['cislo'],
                'city' => $row['mesto'],
                'zip' => $row['psc'],
            ],
            'contact' => [
                'phone' => $row['rezervace'],
                'email' => $row['email'],
                'url' => $row['url'],
            ],
            'description' => $row['popis'],
        ];
    }
}
