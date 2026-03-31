<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

final class DomainTransfer extends Data
{
    public function __construct(
        public readonly string $sld,
        public readonly string $tld,
        public readonly ?string $order_id = null,
        public readonly ?string $status_id = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
