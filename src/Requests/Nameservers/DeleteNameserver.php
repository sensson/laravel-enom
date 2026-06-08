<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Sensson\Enom\Requests\EnomRequest;

final class DeleteNameserver extends EnomRequest
{
    public function __construct(
        private readonly string $nameserver,
    ) {
        //
    }

    protected function command(): string
    {
        return 'DeleteNameServer';
    }

    protected function parameters(): array
    {
        return [
            'NS' => $this->nameserver,
        ];
    }
}
