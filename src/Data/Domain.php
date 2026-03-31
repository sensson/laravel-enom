<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

final class Domain extends Data
{
    public function __construct(
        public string $sld,
        public string $tld,
        public ?string $status = null,
        public ?string $expiration = null,
        public ?bool $auto_renew = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
