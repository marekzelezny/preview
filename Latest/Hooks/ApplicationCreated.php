<?php

namespace Adity\Hooks;

use Adity\Actions\CreateAdityMetaAction;
use Adity\Enums\UserType;
use Adity\Enums\WhatsAppTemplateEnum;
use Adity\Models\Entry;
use Adity\Support\InsuranceUser;
use Adity\Support\Mapper;
use Adity\Traits\Mailable;
use Adity\Traits\ModifiesEntry;
use Adity\Traits\SendsWhatsAppMessages;
use Illuminate\Support\Collection;

class ApplicationCreated extends Hookable
{
    use Mailable, ModifiesEntry, SendsWhatsAppMessages;

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

        if (! $this->isApplicationEntry()) {
            return;
        }

        $this->entry = Entry::get($entry_id);
        $this->meta = Mapper::mapItemMetaToEntryValue($_POST['item_meta'], $form_id);

        if (! $this->isAppOrOfferEntry()) {
            return;
        }

        $this->saveAdityFields();
        $this->saveAdityMeta();

        $this->sendMailToSubscriber(
            application: $this->entry,
            acf_field: 'application_new_subscriber',
        );

        $this->sendMailToInsurance(
            application: $this->entry,
            acf_field: 'application_new_insurance',
        );

        $this->sendWhatsAppMessageToSubscriber(
            application: $this->entry,
            template: WhatsAppTemplateEnum::APPLICATION_NEW
        );

        $this->sendWhatsAppMessageToInsurances(
            application: $this->entry,
            template: WhatsAppTemplateEnum::INSURANCE_NEW_APPLICATION
        );
    }

    public function saveAdityMeta() : void
    {
        CreateAdityMetaAction::handle(
            entry_id: $this->entry_id,
            user_id: $this->entry->user_id,
            user_type: UserType::SUBSCRIBER
        );

        $insurance_ids = InsuranceUser::getAllByCategory($this->entry->category);

        foreach ($insurance_ids as $insurance_id) {
            CreateAdityMetaAction::handle(
                entry_id: $this->entry_id,
                user_id: $insurance_id,
                user_type: UserType::INSURANCE
            );
        }
    }
}
