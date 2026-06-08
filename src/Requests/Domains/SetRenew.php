<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Sensson\Enom\Requests\EnomRequest;

final class SetRenew extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
        private readonly bool $enabled,
    ) {
        //
    }

    protected function command(): string
    {
        return 'SetRenew';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'AutoRenew' => $this->enabled ? '1' : '0',
        ];
    }
}
