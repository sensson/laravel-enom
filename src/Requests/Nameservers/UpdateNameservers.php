<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Saloon\Http\Response;
use Sensson\Enom\Data\Nameservers;
use Sensson\Enom\Requests\EnomRequest;

final class UpdateNameservers extends EnomRequest
{
    /** @param array<string> $nameservers */
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];

        foreach ($this->nameservers as $index => $nameserver) {
            $params['NS'.($index + 1)] = $nameserver;
        }

        return $params;
    }

    public function createDtoFromResponse(Response $response): Nameservers
    {
        return new Nameservers(nameservers: $this->nameservers);
    }
}
