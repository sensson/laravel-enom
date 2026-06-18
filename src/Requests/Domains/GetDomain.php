<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class GetDomain extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetDomainInfo';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): Domain
    {
        $xml = $response->xml();
        $info = $xml->GetDomainInfo;

        return new Domain(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            status: (string) ($info->status->registrationstatus ?? null) ?: null,
            expires_at: (string) ($info->status->expiration ?? null) ?: null,
        );
    }
}
