<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class TransferOrder extends Data
{
    public function __construct(
        public readonly string $order,
        public readonly string $sld,
        public readonly string $tld,
        public readonly ?string $status = null,
        public readonly ?string $status_id = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
