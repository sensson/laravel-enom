<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class AuthCode extends Data
{
    public function __construct(
        public readonly string $sld,
        public readonly string $tld,
        public readonly string $code,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
