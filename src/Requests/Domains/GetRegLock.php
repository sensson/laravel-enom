<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainLock;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

final class GetRegLock extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetRegLock';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): DomainLock
    {
        $xml = $response->xml();

        return new DomainLock(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            locked: (int) $xml->{'reg-lock'} === 1,
        );
    }
}
