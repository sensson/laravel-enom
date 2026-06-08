<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\Transfers\CancelTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrder;

final class TransferResource extends BaseResource
{
    public function get(string $orderId): TransferOrder
    {
        return $this->connector->send(new GetTransferOrder($orderId))->dto();
    }

    public function cancel(string $orderId): void
    {
        $this->connector->send(new CancelTransferOrder($orderId));
    }
}
