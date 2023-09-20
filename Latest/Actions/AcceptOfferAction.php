<?php

namespace Adity\Actions;

use Adity\Actions\ChangeAdityMetaAction;
use Adity\Actions\Emails\AcceptedOfferEmailAction;
use Adity\Enums\StatusEnum;
use Adity\Enums\WhatsAppTemplateEnum;
use Adity\Facades\WhatsApp;
use Adity\Models\AnalyticsItem;
use Adity\Models\Entry;
use Adity\Traits\SendsWhatsAppMessages;

class AcceptOfferAction
{
    use SendsWhatsAppMessages;

    public static function handle(int $offer_id, int $application_id) : void
    {
        $offer = Entry::get($offer_id);
        $application = Entry::get($application_id);
        $insurance_winner_id = $offer->user_id;
        $final_price = $offer->getPrice();

        /**
         * Update offer status
         */
        self::updateDataFor($offer, $insurance_winner_id);

        /**
         * Update application status
         */
        self::updateDataFor($application, $insurance_winner_id, $final_price);

        /**
         * Send emails
         */
        AcceptedOfferEmailAction::handle(
            offer: $offer,
            application: $application
        );

        /**
         * Send WhatsApp messages
         */
        self::sendWhatsAppMessageTo(
            user: $application->user(),
            template: WhatsAppTemplateEnum::OFFER_ACCEPTED
        );

        self::sendWhatsAppMessageTo(
            user: $offer->user(),
            template: WhatsAppTemplateEnum::INSURANCE_OFFER_ACCEPTED
        );

        /**
         * Updates Analytics
         */
        AnalyticsItem::updateOrCreate(
            [
                'entry_id' => $application_id,
            ],
            [
                'status' => StatusEnum::ACCEPTED->value,
                'price' => $offer->getPrice() ?? 0,
                'price_type' => $offer->getPriceType() ?? null,
            ]
        );

        AnalyticsItem::updateOrCreate(
            [
                'entry_id' => $offer_id,
            ],
            [
                'status' => StatusEnum::ACCEPTED->value,
            ]
        );
    }

    public static function updateDataFor($entry, $insurance_winner_id, $final_price = null)
    {
        if ($entry->values->where('key', 'status')->first()) {
            $entry->updateMeta(
                $entry->values->where('key', 'status')->first()?->id,
                ['status' => StatusEnum::ACCEPTED->value]
            );
        }

        // Sets offer winning price in application in database
        if ($final_price) {
            global $wpdb;

            $wpdb->update(
                $wpdb->prefix . 'frm_items',
                ['adity_price_final' => $final_price],
                ['id' => $entry->id]
            );
        }

        ChangeAdityMetaAction::forSubscriber($entry->id, [
            'status' => StatusEnum::ACCEPTED->value,
        ]);

        ChangeAdityMetaAction::forInsurances($entry->id, [
            'status' => StatusEnum::HISTORICAL->value,
        ]);

        ChangeAdityMetaAction::forOneInsurance($entry->id, $insurance_winner_id, [
            'status' => StatusEnum::ACCEPTED->value,
        ]);
    }
}
