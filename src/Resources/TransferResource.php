<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Illuminate\Support\Collection;
use Saloon\Http\BaseResource;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\TransferOrder;
use Sensson\Enom\Requests\Transfers\CancelTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrder;
use Sensson\Enom\Requests\Transfers\GetTransferOrdersByDomain;

class TransferResource extends BaseResource
{
    /**
     * @return Collection<int, TransferOrder>
     */
    public function all(string $sld, string $tld): Collection
    {
        return collect($this->connector->send(new GetTransferOrdersByDomain(new DomainName($sld, $tld)))->dto());
    }

    public function latest(string $sld, string $tld): ?TransferOrder
    {
        // Orders without a parseable date sort first (PHP_INT_MIN), so the
        // stable sort keeps them in response order and a dated order always
        // wins. Not interchangeable with sortByDesc()->first(): on ties that
        // would return the first response item instead of the last.
        return $this->all($sld, $tld)
            ->sortBy(fn (TransferOrder $order): int => $order->orderedAt()?->getTimestamp() ?? PHP_INT_MIN)
            ->last();
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
