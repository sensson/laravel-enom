<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainAvailability;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

final class CheckDomain extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'Check';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): DomainAvailability
    {
        $xml = $response->xml();

        return new DomainAvailability(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            available: (int) $xml->RRPCode === 210,
        );
    }
}
