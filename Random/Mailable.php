<?php

namespace Adity\Traits;

use WP_User;
use FrmProEntriesController;
use Adity\Mailer\Message;
use Adity\Models\EmailSettings;
use Adity\Models\Entry;
use Adity\Models\User;
use Adity\Support\InsuranceUser;

trait Mailable
{
    public function sendMailToInsurance(Entry $application, string $acf_field, array $options = []) : void
    {
        $settings = self::getAcfField($acf_field);

        if (! $settings->activated) {
            return;
        }

        InsuranceUser::getAllByApplication($application->id)
            ->each(function ($insurance) use ($settings, $application, $options) {
                $user = new User($insurance->user_id);

                $default_options = self::mapContent(
                    application: $application,
                    type: 'insurance',
                    user_id: $insurance->user_id
                );

                $sending_options = array_merge($default_options, $options);

                self::sendEmailTo(
                    to: $user->user_email,
                    settings: $settings,
                    content: $sending_options,
                );
            });
    }

    public function sendMailToSubscriber(Entry $application, string $acf_field, array $options = []) : void
    {
        $settings = self::getAcfField($acf_field);

        if (! $settings->activated) {
            return;
        }

        $default_options = self::mapContent(
            application: $application,
            type: 'subscriber',
        );

        $sending_options = array_merge($default_options, $options);

        self::sendEmailTo(
            to: $application->user()->user_email,
            settings: $settings,
            content: $sending_options,
        );
    }

    public static function getAcfField(string $acf_field) : EmailSettings
    {
        return new EmailSettings($acf_field);
    }

    public static function sendEmailTo(string $to, EmailSettings $settings, array $content = []) : void
    {
        Message::create()
            ->to($to)
            ->bcc($settings->recipients)
            ->subject($settings->subject)
            ->message($settings->message)
            ->content($content)
            ->send();
    }

    public static function mapContent(Entry $application, string $type, int $user_id = null)
    {
        $default = [
            'application_id' => $application->id,
            'application_category' => $application->category,
            'application_detail_link' => $application->getDetailLink(),
            'application_form_table' => FrmProEntriesController::show_entry_shortcode([
                'id' => $application->id,
                'plain_text' => 0,
                'format' => 'text',
                'user_info' => 0,
                'include_blank' => 0,
            ]),
        ];

        if ($type === 'subscriber') {
            $user = $application->user();

            $subscriber = [
                'user_firstname' => $user->user_firstname,
                'user_lastname' => $user->user_lastname,
                'user_phone' => $user->user_phone,
                'user_email' => $user->user_email,
            ];

            return array_merge($default, $subscriber);
        }

        if ($type == 'insurance') {
            $user = new User($user_id);

            $insurance = [
                'insurance_company_name' => $user->meta('insurance_company_name'),
                'insurance_user_firstname' => $user->user_firstname,
                'insurance_user_lastname' => $user->user_lastname,
                'insurance_phone' => $user->meta('phone_number'),
                'insurance_email' => $user->user_email,
            ];

            return array_merge($default, $insurance);
        }
    }
}
