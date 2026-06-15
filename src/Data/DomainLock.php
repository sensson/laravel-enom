<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class DomainLock extends Data
{
    public function __construct(
        public string $sld,
        public string $tld,
        public bool $locked,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
