<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class DomainTransfer extends Data
{
    public function __construct(
        public readonly string $sld,
        public readonly string $tld,
        public readonly ?string $status = null,
        public readonly ?string $requested_at = null,
        public readonly ?string $requested_by = null,
        public readonly ?string $expires_at = null,
        public readonly ?string $action_at = null,
        public readonly ?string $action_by = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
