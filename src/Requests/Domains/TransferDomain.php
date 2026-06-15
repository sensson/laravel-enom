<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Requests\EnomRequest;

class TransferDomain extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
        private readonly string $code,
    ) {
        //
    }

    protected function command(): string
    {
        return 'TP_CreateOrder';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'OrderType' => 'AutoVerification',
            'DomainPassword' => $this->code,
        ];
    }

    public function createDtoFromResponse(Response $response): DomainTransfer
    {
        $xml = $response->xml();

        return new DomainTransfer(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            status: (string) ($xml->TransferOrder->statusid ?? null) ?: null,
        );
    }
}
