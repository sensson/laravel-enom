<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\Transfers\CancelTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrdersByDomain;

class TransferResource extends BaseResource
{
    /**
     * @return array<TransferOrder>
     */
    public function list(string $sld, string $tld): array
    {
        return $this->connector->send(new GetTransferOrdersByDomain(new DomainName($sld, $tld)))->dto();
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
