<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class AccountBalance extends Data
{
    public function __construct(
        public readonly float $balance,
        public readonly string $currency,
    ) {
        //
    }
}
