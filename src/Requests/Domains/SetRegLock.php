<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainLock;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class SetRegLock extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
        protected bool $locked,
    ) {
        //
    }

    protected function command(): string
    {
        return 'SetRegLock';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'RegLock' => $this->locked ? '1' : '0',
        ];
    }

    public function createDtoFromResponse(Response $response): DomainLock
    {
        return new DomainLock(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            locked: $this->locked,
        );
    }
}
