<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Dnssec;

use Saloon\Http\Response;
use Sensson\Enom\Data\Dnssec;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class AddDnsSec extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
        protected Dnssec $record,
    ) {
        //
    }

    protected function command(): string
    {
        return 'AddDnsSec';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'KeyTag' => $this->record->key_tag,
            'Alg' => $this->record->algorithm,
            'DigestType' => $this->record->digest_type,
            'Digest' => $this->record->digest,
        ];
    }

    public function createDtoFromResponse(Response $response): Dnssec
    {
        return $this->record;
    }
}
