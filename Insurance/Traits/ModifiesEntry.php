<?php

namespace Adity\Traits;

use Adity\Actions\UpdateEntry;
use Adity\Enums\FormEnum;

trait ModifiesEntry
{
    public function saveAdityFields() : void
    {
        $guid = $this->meta->where('type', 'guid')->first()?->value ?? null;
        $edit_page_id = $this->meta->where('type', 'page_id')->first()?->value ?? null;
        $category = $this->entry->category ?? null;

        UpdateEntry::handle($this->entry_id, [
            'adity_category' => $category,
            'adity_guid' => $guid,
            'adity_edit_page_id' => $edit_page_id,
        ]);
    }

    public function isOfferEntry() : bool
    {
        return $this->form_id === FormEnum::INSURANCE_REPLY_FORM->value;
    }

    public function isApplicationEntry() : bool
    {
        return $this->form_id !== FormEnum::INSURANCE_REPLY_FORM->value;
    }

    public function isAppOrOfferEntry() : bool
    {
        return $this->meta->where('label', 'Adity ID')->count() > 0 || $this->meta->where('label', 'Application ID')->count() > 0;
    }

    public function getTypeForAnalytics() : string
    {
        return $this->isApplicationEntry() ? 'application' : 'offer';
    }
}
