<?php

namespace Adity\Repositories;

use Adity\Models\Entry;
use Adity\Support\Formiddable;
use FrmEntry;
use Illuminate\Support\Collection;

class EntryRepository extends Collection
{
    public static function getAll(...$ats)
    {
        $entries = FrmEntry::getAll(...$ats);
        $output = [];

        if ($entries) {
            foreach ($entries as $entry) {
                $output[] = Entry::get($entry->id);
            }
        }

        return new self($output);
    }

    public function withGuid() : self
    {
        return $this->filter(function ($item) {
            return empty($item->guid) === false;
        });
    }

    public function hasStatus() : self
    {
        return $this->filter(function ($item) {
            return empty($item->status) === false;
        });
    }

    public function skipOfferForCurrentInsurance($application_id) : self
    {
        $offer = Formiddable::getInsuranceFormForCurrentUser($application_id);

        if (! $offer) {
            return $this;
        }

        return $this->filter(function ($entry) use ($offer) {
            return $entry->id != $offer->id;
        });
    }
}
