<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Nameserver extends Data
{
    public function __construct(
        public readonly string $host,
        public readonly ?string $ip = null,
    ) {
        //
    }
}
