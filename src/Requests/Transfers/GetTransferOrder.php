<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Transfers;

use Saloon\Http\Response;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\EnomRequest;

class GetTransferOrder extends EnomRequest
{
    public function __construct(
        protected string $order,
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
            'TransferOrderID' => $this->order,
        ];
    }

    public function createDtoFromResponse(Response $response): TransferOrder
    {
        $xml = $response->xml()->TP_GetOrder->TransferOrder;

        return new TransferOrder(
            order: $this->field($xml, 'OrderID', 'transferorderid') ?? '',
            sld: (string) $xml->SLD,
            tld: (string) $xml->TLD,
            status: $this->field($xml, 'status', 'orderstatus'),
            status_id: $this->field($xml, 'statusid'),
            ordered_at: $this->field($xml, 'orderdate'),
        );
    }
}
