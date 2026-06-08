<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainAvailability;
use Sensson\Enom\Requests\EnomRequest;

final class CheckDomain extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): DomainAvailability
    {
        $xml = $response->xml();

        return new DomainAvailability(
            sld: $this->sld,
            tld: $this->tld,
            available: (int) $xml->RRPCode === 210,
        );
    }
}
