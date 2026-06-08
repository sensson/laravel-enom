<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

final class RenewDomain extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
        private readonly int $years = 1,
    ) {
        //
    }

    protected function command(): string
    {
        return 'Extend';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'NumYears' => $this->years,
        ];
    }

    public function createDtoFromResponse(Response $response): Domain
    {
        return new Domain(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
        );
    }
}
