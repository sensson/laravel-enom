<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Transfers;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\EnomRequest;

class GetTransferOrdersByDomain extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
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
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    /** @return array<TransferOrder> */
    public function createDtoFromResponse(Response $response): array
    {
        $xml = $response->xml()->TP_GetOrdersByDomain;
        $orders = [];

        foreach ($xml->TransferOrder ?? [] as $order) {
            $orders[] = new TransferOrder(
                order: $this->field($order, 'transferorderid', 'OrderID') ?? '',
                sld: $this->domain->sld,
                tld: $this->domain->tld,
                status: $this->field($order, 'orderstatus', 'status'),
                status_id: $this->field($order, 'statusid'),
                ordered_at: $this->field($order, 'orderdate'),
            );
        }

        return $orders;
    }
}
