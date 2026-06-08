<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Sensson\Enom\Enums\DnsRecordType;
use Spatie\LaravelData\Data;

final class DnsRecord extends Data
{
    public function __construct(
        public readonly string $hostname,
        public readonly DnsRecordType $type,
        public readonly string $address,
        public readonly int $ttl = 300,
        public readonly ?int $mx_preference = null,
    ) {
        //
    }
}
