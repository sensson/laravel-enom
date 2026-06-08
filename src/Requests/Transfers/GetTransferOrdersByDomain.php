<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Transfers;

use Saloon\Http\Response;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\EnomRequest;

final class GetTransferOrdersByDomain extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
    ) {
        //
    }

    protected function command(): string
    {
        return 'TP_GetOrdersByDomain';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    /** @return array<TransferOrder> */
    public function createDtoFromResponse(Response $response): array
    {
        $xml = $response->xml()->TP_GetOrdersByDomain;
        $orders = [];

        foreach ($xml->TransferOrder ?? [] as $order) {
            $orders[] = new TransferOrder(
                order_id: (string) $order->OrderID,
                sld: $this->sld,
                tld: $this->tld,
                status: (string) ($order->status ?? null) ?: null,
                status_id: (string) ($order->statusid ?? null) ?: null,
            );
        }

        return $orders;
    }
}
