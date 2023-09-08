<?php

namespace Adity\Actions;

use Adity\Actions\ChangeAdityMetaAction;
use Adity\Actions\Emails\AcceptedOfferEmailAction;
use Adity\Enums\StatusEnum;
use Adity\Models\Entry;

class AcceptOfferAction
{
    public static function handle(int $offer_id, int $application_id) : void
    {
        $offer = Entry::get($offer_id);
        $application = Entry::get($application_id);
        $insurance_winner_id = $offer->user_id;

        /**
         * Update offer status
         */
        self::updateDataFor($offer, $insurance_winner_id);

        /**
         * Update application status
         */
        self::updateDataFor($application, $insurance_winner_id);

        /**
         * Send emails
         */
        AcceptedOfferEmailAction::handle(
            offer: $offer,
            application: $application
        );
    }

    public static function updateDataFor($entry, $insurance_winner_id)
    {
        if ($entry->values->where('key', 'status')->first()) {
            $entry->updateMeta(
                $entry->values->where('key', 'status')->first()?->id,
                ['status' => StatusEnum::ACCEPTED->value]
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
