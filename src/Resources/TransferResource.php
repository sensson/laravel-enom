<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\Transfers\CancelTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrdersByDomain;

class TransferResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
        protected readonly DomainName $domain,
    ) {
        //
    }

    /** @return array<TransferOrder> */
    public function list(): array
    {
        return $this->connector->send(new GetTransferOrdersByDomain($this->domain))->dto();
    }

    public function get(string $order): TransferOrder
    {
        return $this->connector->send(new GetTransferOrder($order))->dto();
    }

    public function cancel(string $order): void
    {
        $this->connector->send(new CancelTransferOrder($order));
    }
}
