<?php

namespace Adity\Enums;

enum EmailMessageEnum: string
{
    case ACCEPTED_APPLICATION = 'application_accepted';
    case ADMIN_APPLICATION_REMOVAL = 'admin_application_removal';
    case ADMIN_APPLICATION_MODIFICATION = 'admin_application_modification';

    public function getAcfMessageFor(string $groupKey)
    {
        $acf = $this->getAcfMessage();

        if (is_array($acf)) {
            return $acf[$groupKey];
        }

        return $acf;
    }

    public function getAcfMessage()
    {
        return get_field($this->value, 'option');
    }
}
