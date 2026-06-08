<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Requests\EnomRequest;

final class TransferDomain extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'OrderType' => 'AutoVerification',
            'DomainPassword' => $this->code,
        ];
    }

    public function createDtoFromResponse(Response $response): DomainTransfer
    {
        $xml = $response->xml();

        return new DomainTransfer(
            sld: $this->sld,
            tld: $this->tld,
            order_id: (string) ($xml->OrderID ?? null) ?: null,
            status_id: (string) ($xml->TransferOrder->statusid ?? null) ?: null,
        );
    }
}
