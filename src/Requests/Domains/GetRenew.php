<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class GetRenew extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetRenew';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): bool
    {
        $xml = $response->xml();

        return (int) $xml->auto_renew === 1;
    }
}
