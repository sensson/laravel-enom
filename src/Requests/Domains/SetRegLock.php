<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\DomainLock;
use Sensson\Enom\Requests\EnomRequest;

final class SetRegLock extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
        private readonly bool $locked,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'RegLock' => $this->locked ? '1' : '0',
        ];
    }

    public function createDtoFromResponse(Response $response): DomainLock
    {
        return new DomainLock(
            sld: $this->sld,
            tld: $this->tld,
            locked: $this->locked,
        );
    }
}
