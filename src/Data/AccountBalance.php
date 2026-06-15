<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class AccountBalance extends Data
{
    public function __construct(
        public float $amount,
        public ?float $credit = null,
        public ?float $credit_threshold = null,
        public string $currency = 'USD',
    ) {
        //
    }
}
