<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class PushDomain extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
        protected string $account,
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
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'AccountName' => $this->account,
        ];
    }
}
