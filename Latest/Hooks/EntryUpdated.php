<?php

namespace Adity\Hooks;

use Adity\Actions\ChangeAdityMetaAction;
use Adity\Actions\ChangeTagAction;
use Adity\Actions\ConnectOfferWithAppAction;
use Adity\Enums\FormEnum;
use Adity\Enums\StatusEnum;
use Adity\Enums\TagEnum;
use Adity\Models\AnalyticsItem;
use Adity\Models\Entry;
use Adity\Support\Mapper;
use Adity\Traits\ModifiesEntry;
use Illuminate\Support\Collection;

class EntryUpdated extends Hookable
{
    use ModifiesEntry;

    public string $hook = 'frm_after_update_entry';
    public string $type = 'action';
    public int $priority = 30;
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
        $this->meta = Mapper::mapItemMetaToEntryValue($_POST['item_meta'], $form_id);

        $status = $this->meta->where('type', 'status')->first()?->value ?? StatusEnum::ACTIVE->value;

        AnalyticsItem::updateOrCreate(
            [
                'entry_id' => $entry_id,
            ],
            [
                'type' => $this->getTypeForAnalytics(),
                'entry_id' => $entry_id,
                'parent_entry_id' => $this->entry->parent_item_id ?? 0,
                'user_id' => $this->entry->user_id,
                'status' => $status,
                'price' => $this->entry->getPrice() ?? 0,
                'price_type' => $this->entry->getPriceType() ?? null,
            ]
        );
    }
}
