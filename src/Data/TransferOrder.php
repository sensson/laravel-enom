<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class TransferOrder extends Data
{
    public function __construct(
        public string $order,
        public string $sld,
        public string $tld,
        public ?string $status = null,
        public ?string $status_id = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
