<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Transfers;

use Saloon\Http\Response;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\EnomRequest;

final class GetTransferOrder extends EnomRequest
{
    public function __construct(
        private readonly string $orderId,
    ) {
        //
    }

    protected function command(): string
    {
        return 'TP_GetOrder';
    }

    protected function parameters(): array
    {
        return [
            'TransferOrderID' => $this->orderId,
        ];
    }

    public function createDtoFromResponse(Response $response): TransferOrder
    {
        $xml = $response->xml()->TP_GetOrder->TransferOrder;

        return new TransferOrder(
            order_id: (string) $xml->OrderID,
            sld: (string) $xml->SLD,
            tld: (string) $xml->TLD,
            status: (string) ($xml->status ?? null) ?: null,
            status_id: (string) ($xml->statusid ?? null) ?: null,
        );
    }
}
