<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Sensson\Enom\Requests\EnomRequest;

final class PushDomain extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
        private readonly string $account,
    ) {
        //
    }

    protected function command(): string
    {
        return 'PushDomain';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'AccountName' => $this->account,
        ];
    }
}
