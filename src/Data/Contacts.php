<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

final class Contacts extends Data
{
    public function __construct(
        public readonly Contact $registrant,
        public readonly Contact $admin,
        public readonly Contact $tech,
        public readonly Contact $billing,
    ) {
        //
    }
}
