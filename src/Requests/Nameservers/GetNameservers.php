<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\Nameserver;
use Sensson\Enom\Requests\EnomRequest;

class GetNameservers extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetDNS';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    /**
     * @return array<Nameserver>
     */
    public function createDtoFromResponse(Response $response): array
    {
        $xml = $response->xml()->GetDNS->dns;
        $nameservers = [];

        foreach (range(1, 12) as $index) {
            $key = "NS{$index}";
            $value = (string) ($xml->{$key} ?? null);

            if ($value !== '') {
                $nameservers[] = new Nameserver(host: $value);
            }
        }

        return $nameservers;
    }
}
