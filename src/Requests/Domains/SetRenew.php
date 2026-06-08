<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

final class SetRenew extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
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
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'AutoRenew' => $this->enabled ? '1' : '0',
        ];
    }
}
