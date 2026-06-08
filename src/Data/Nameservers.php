<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

final class Nameservers extends Data
{
    /** @param array<string> $nameservers */
    public function __construct(
        public readonly array $nameservers,
    ) {
        //
    }
}
