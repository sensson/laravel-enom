<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Saloon\Http\Response;
use Sensson\Enom\Data\Nameservers;
use Sensson\Enom\Requests\EnomRequest;

final class GetNameservers extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): Nameservers
    {
        $xml = $response->xml()->GetDNS->dns;
        $nameservers = [];

        foreach (range(1, 12) as $index) {
            $key = "NS{$index}";
            $value = (string) ($xml->{$key} ?? null);

            if ($value !== '') {
                $nameservers[] = $value;
            }
        }

        return new Nameservers(nameservers: $nameservers);
    }
}
