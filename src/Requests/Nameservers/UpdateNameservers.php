<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\Nameserver;
use Sensson\Enom\Requests\EnomRequest;

class UpdateNameservers extends EnomRequest
{
    /**
     * @param  array<Nameserver>  $nameservers
     */
    public function __construct(
        private readonly DomainName $domain,
        private readonly array $nameservers,
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

        foreach (array_values($this->nameservers) as $index => $nameserver) {
            $params['NS'.($index + 1)] = $nameserver->host;
        }

        return $params;
    }

    /**
     * @return array<Nameserver>
     */
    public function createDtoFromResponse(Response $response): array
    {
        return $this->nameservers;
    }
}
