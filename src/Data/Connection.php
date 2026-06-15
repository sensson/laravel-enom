<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Connection extends Data
{
    public function __construct(
        public string $username,
        public string $token,
        public bool $sandbox = true,
    ) {
        //
    }
}
