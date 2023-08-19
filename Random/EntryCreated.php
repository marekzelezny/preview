<?php

namespace App\Hooks;

use App\Actions\ChangeTagAction;
use App\Actions\ConnectOfferWithAppAction;
use App\Actions\UpdateEntry;
use App\Enums\FormEnum;
use App\Enums\TagEnum;
use App\Models\Entry;
use App\Support\Mapper;
use Illuminate\Support\Collection;

class EntryCreated extends Hookable
{
    public string $hook = 'frm_after_create_entry';
    public string $type = 'action';
    public int $priority = 20;
    public int $accepted_args = 2;
    public int $entry_id;
    public int $form_id;
    public Entry $entry;
    public Collection $meta;

    public function handle($entry_id, $form_id) : void
    {
        $this->entry_id = $entry_id;
        $this->form_id = $form_id;

        $this->entry = Entry::get($entry_id);
        $this->meta = Mapper::toEntryValue($_POST['item_meta'], $form_id);

        $this->saveFields();
        $this->associateOfferWithApplication();
        $this->updateParentEntry();
    }

    /**
     * Saves fields (Status, GUID) into the database table
     */
    public function saveFields() : void
    {
        $status = $this->meta->where('type', 'status')->first()?->value ?? null;
        $guid = $this->meta->where('type', 'guid')->first()?->value ?? null;
        $edit_page_id = $this->meta->where('type', 'page_id')->first()?->value ?? null;
        $tag = TagEnum::NEW;

        UpdateEntry::handle($this->entry_id, [
            'status' => $status,
            'guid' => $guid,
            'tag' => $tag->name,
            'edit_page_id' => $edit_page_id ?? '',
        ]);
    }

    /**
     * Creates a connection between offers and applications
     */
    public function associateOfferWithApplication() : void
    {
        // Skip if not Insurance reply form
        if ($this->form_id != FormEnum::INSURANCE_REPLY_FORM->value) {
            return;
        }

        $application_guid = $this->meta->where('label', 'Application ID')->first()?->value ?? null;

        if (! $application_guid) {
            return;
        }

        ConnectOfferWithAppAction::handle(
            $this->entry_id,
            $application_guid
        );
    }

    /**
     * Updates application connected with offer with a new tag
     */
    public function updateParentEntry() : void
    {
        if (! $this->entry->parent_item_id) {
            return;
        }

        ChangeTagAction::handle(
            TagEnum::NEW ,
            $this->entry->parent_item_id,
        );
    }
}
