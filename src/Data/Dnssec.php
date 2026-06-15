<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Dnssec extends Data
{
    public function __construct(
        public int $key_tag,
        public int $algorithm,
        public int $digest_type,
        public string $digest,
    ) {
        //
    }
}
