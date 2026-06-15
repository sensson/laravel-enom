<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Domain extends Data
{
    /**
     * @param  array<string>  $nameservers
     * @param  array<Dnssec>  $dnssec
     */
    public function __construct(
        public readonly string $sld,
        public readonly string $tld,
        public readonly ?string $status = null,
        public readonly ?string $registrant = null,
        public readonly ?string $admin_contact = null,
        public readonly ?string $tech_contact = null,
        public readonly ?string $billing_contact = null,
        public readonly array $nameservers = [],
        public readonly ?bool $locked = null,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
        public readonly ?string $expires_at = null,
        public readonly ?string $auth_code = null,
        public readonly array $dnssec = [],
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
