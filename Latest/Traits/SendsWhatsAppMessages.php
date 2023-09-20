<?php

namespace Adity\Traits;

use Adity\Enums\WhatsAppTemplateEnum;
use Adity\Facades\WhatsApp;
use Adity\Models\Entry;
use Adity\Models\User;
use Adity\Support\InsuranceUser;

trait SendsWhatsAppMessages
{
    public static function sendWhatsAppMessageTo(User $user, WhatsAppTemplateEnum $template)
    {
        if (! $user->canReceiveWhatsAppMessages()) {
            return;
        }

        $whatsapp = WhatsApp::init();

        $whatsapp->sendTemplate(
            to: $user->getPhoneNumber(),
            template_name: $template->value,
            language: 'es_ES',
        );
    }

    public function sendWhatsAppMessageToSubscriber(Entry $application, WhatsAppTemplateEnum $template)
    {
        self::sendWhatsAppMessageTo(
            user: $application->user(),
            template: $template
        );
    }

    public function sendWhatsAppMessageToInsurances(Entry $application, WhatsAppTemplateEnum $template)
    {
        InsuranceUser::getAllByApplication($application->id)
            ->each(function ($insurance) use ($template) {
                $user = new User($insurance->user_id);

                self::sendWhatsAppMessageTo(
                    user: $user,
                    template: $template
                );
            });
    }

    public function sendWhatsAppMessageToParticipatingInsurances(Entry $application, WhatsAppTemplateEnum $template)
    {
        $insurances = InsuranceUser::getParticipatingInsurancesByApplication($application->id);

        if (! $insurances) {
            return;
        }

        foreach ($insurances as $insurance) {
            $user = new User($insurance);

            self::sendWhatsAppMessageTo(
                user: $user,
                template: $template
            );
        }
    }
}
