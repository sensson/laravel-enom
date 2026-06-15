<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class DomainTransfer extends Data
{
    public function __construct(
        public string $sld,
        public string $tld,
        public ?string $status = null,
        public ?string $requested_at = null,
        public ?string $requested_by = null,
        public ?string $expires_at = null,
        public ?string $action_at = null,
        public ?string $action_by = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
