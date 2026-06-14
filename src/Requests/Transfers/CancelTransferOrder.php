<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Transfers;

use Sensson\Enom\Requests\EnomRequest;

class CancelTransferOrder extends EnomRequest
{
    public function __construct(
        private readonly string $order,
    ) {
        //
    }

    protected function command(): string
    {
        return 'TP_CancelOrder';
    }

    protected function parameters(): array
    {
        return [
            'TransferOrderID' => $this->order,
        ];
    }
}
