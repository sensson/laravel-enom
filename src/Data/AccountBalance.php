<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class AccountBalance extends Data
{
    public function __construct(
        public readonly float $amount,
        public readonly ?float $credit = null,
        public readonly ?float $credit_threshold = null,
        public readonly string $currency = 'USD',
    ) {
        //
    }
}
