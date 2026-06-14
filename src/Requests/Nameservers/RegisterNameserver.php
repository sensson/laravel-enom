<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Nameservers;

use Sensson\Enom\Requests\EnomRequest;

class RegisterNameserver extends EnomRequest
{
    public function __construct(
        private readonly string $nameserver,
        private readonly string $ip,
    ) {
        //
    }

    protected function command(): string
    {
        return 'RegisterNameServer';
    }

    protected function parameters(): array
    {
        return [
            'NS' => $this->nameserver,
            'IP' => $this->ip,
        ];
    }
}
