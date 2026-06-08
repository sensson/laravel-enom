<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\Transfers\CancelTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrdersByDomain;

final class TransferResource extends BaseResource
{
    public function get(string $orderId): TransferOrder
    {
        return $this->connector->send(new GetTransferOrder($orderId))->dto();
    }

    /** @return array<TransferOrder> */
    public function getByDomain(string $sld, string $tld): array
    {
        return $this->connector->send(new GetTransferOrdersByDomain($sld, $tld))->dto();
    }

    public function cancel(string $orderId): void
    {
        $this->connector->send(new CancelTransferOrder($orderId));
    }
}
