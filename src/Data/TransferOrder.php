<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Spatie\LaravelData\Data;

class TransferOrder extends Data
{
    public function __construct(
        public string $order,
        public string $sld,
        public string $tld,
        public ?string $status = null,
        public ?string $status_id = null,
        public ?string $ordered_at = null,
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }

    public function orderedAt(): ?CarbonImmutable
    {
        // Carbon parses '' and whitespace as "now", which would make a
        // date-less order the newest one.
        if (trim((string) $this->ordered_at) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($this->ordered_at);
        } catch (InvalidFormatException) {
            return null;
        }
    }
}
