<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\Nameservers;
use Sensson\Enom\Requests\EnomRequest;

final class UpdateNameservers extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
        private readonly Nameservers $nameservers,
    ) {
        //
    }

    protected function command(): string
    {
        return 'ModifyNS';
    }

    protected function parameters(): array
    {
        $params = [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];

        foreach ($this->nameservers->nameservers as $index => $nameserver) {
            $params['NS'.($index + 1)] = $nameserver;
        }

        return $params;
    }

    public function createDtoFromResponse(Response $response): Nameservers
    {
        return $this->nameservers;
    }
}
