<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Requests\EnomRequest;

final class GetRenew extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetRenew';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): bool
    {
        $xml = $response->xml();

        return (int) $xml->auto_renew === 1;
    }
}
