<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Domain extends Data
{
    /** @param array<Dnssec> $dnssec */
    public function __construct(
        public readonly string $sld,
        public readonly string $tld,
        public readonly ?string $status = null,
        public readonly ?string $expiration = null,
        public readonly ?bool $auto_renew = null,
        public readonly array $dnssec = [],
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
