<?php

namespace Adity\Repositories;

use Adity\Enums\StatusEnum;
use Adity\Models\EntryValue;
use Illuminate\Support\Collection;

class FieldsRepository extends Collection
{
    /**
     * Default settings set in Adity\Models\Entry::mapValuesToFields()
     */
    public function withFormOrder() : self
    {
        return $this->sortBy('order');
    }

    public function onlyVisible() : self
    {
        return $this->filter(function ($item) {
            return $item->hidden === false;
        });
    }

    public function withLabelsOnly() : self
    {
        return $this->filter(function ($item) {
            return empty($item->label) === false;
        });
    }

    public function getStatus() : ?EntryValue
    {
        return $this->firstWhere('type', 'status');
    }

    public function withMainOnly() : self
    {
        return $this->filter(function ($item) {
            return $item->isMain === true;
        });
    }

    public function withoutMain() : self
    {
        return $this->filter(function ($item) {
            return $item->isMain === false;
        });
    }

    public function hideContactDetails() : self
    {
        return $this->filter(function ($item) {
            return $item->contactDetail === false;
        });
    }
}
