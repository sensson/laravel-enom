<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Enom\Data\DomainTransfer;

final class TransferDomain extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $sld,
        protected readonly string $tld,
        protected readonly string $code,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/interface.asp';
    }

    protected function defaultQuery(): array
    {
        return [
            'Command' => 'TP_CreateOrder',
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
