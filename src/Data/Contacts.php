<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Contacts extends Data
{
    public function __construct(
        public Contact $registrant,
        public Contact $admin,
        public Contact $tech,
        public Contact $billing,
    ) {
        //
    }
}
