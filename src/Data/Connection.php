<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Connection extends Data
{
    public function __construct(
        public readonly string $username,
        public readonly string $token,
        public readonly bool $sandbox = true,
    ) {
        //
    }
}
