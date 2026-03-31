<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class RenewDomain extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $sld,
        protected readonly string $tld,
        protected readonly int $years = 1,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/interface.asp';
    }

    protected function defaultQuery(): array
    {
        return [
            'Command' => 'Extend',
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'NumYears' => $this->years,
        ];
    }
}
