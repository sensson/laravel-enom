<?php

declare(strict_types=1);

namespace Sensson\Enom\Data;

use Spatie\LaravelData\Data;

class Domain extends Data
{
    /**
     * @param  array<Nameserver>  $nameservers
     * @param  array<Dnssec>  $dnssec
     */
    public function __construct(
        public string $sld,
        public string $tld,
        public ?string $status = null,
        public ?string $registrant = null,
        public ?string $admin_contact = null,
        public ?string $tech_contact = null,
        public ?string $billing_contact = null,
        public array $nameservers = [],
        public ?bool $locked = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?string $expires_at = null,
        public ?string $auth_code = null,
        public array $dnssec = [],
    ) {
        //
    }

    public function name(): string
    {
        return "{$this->sld}.{$this->tld}";
    }
}
