<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

final class DomainName extends Data
{
    public function __construct(
        public readonly string $sld,
        public readonly string $tld,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
