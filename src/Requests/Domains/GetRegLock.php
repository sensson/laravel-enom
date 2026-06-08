<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainLock;
use Sensson\Enom\Requests\EnomRequest;

final class GetRegLock extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): DomainLock
    {
        $xml = $response->xml();

        return new DomainLock(
            sld: $this->sld,
            tld: $this->tld,
            locked: (int) $xml->{'reg-lock'} === 1,
        );
    }
}
