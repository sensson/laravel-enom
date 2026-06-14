<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Dnssec extends Data
{
    public function __construct(
        public readonly int $key_tag,
        public readonly int $algorithm,
        public readonly int $digest_type,
        public readonly string $digest,
    ) {
        //
    }
}
