<?php

namespace Adity\Enums;

enum WhatsAppTemplateEnum: string
{
    // Subscriber: New application
    case APPLICATION_NEW = 'cliente_solicitud_creada';

    // Subscriber: Deleted application
    case APPLICATION_DELETED = 'cliente_solicitud_eliminada';

    // Subscriber: New Offer
    case OFFER_NEW = 'cliente_oferta_nueva';

    // Subscriber: Offer accepted by subscriber
    case OFFER_ACCEPTED = 'cliente_oferta_aceptada';

    // Subscriber: Admin modified application
    case ADMIN_APPLICATION_MODIFIED = 'cliente_solicitud_modificada_admin';

    // Subscriber: Admin deleted application
    case ADMIN_APPLICATION_DELETED = 'cliente_solicitud_eliminada_admin';

    // Insurance: New Application
    case INSURANCE_NEW_APPLICATION = 'colaborador_solicitud_nuvea';

    // Insurance: Offer has been accepted by subscriber (only for winning insurance)
    case INSURANCE_OFFER_ACCEPTED = 'colaborador_oferta_aceptada';
}
