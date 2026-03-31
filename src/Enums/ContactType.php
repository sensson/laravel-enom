<?php

declare(strict_types=1);

namespace Sensson\Enom\Enums;

enum ContactType: string
{
    case Registrant = 'Registrant';
    case Admin = 'Admin';
    case Tech = 'Tech';
    case Billing = 'AuxBilling';
}
