<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\AuthCode;
use Sensson\Enom\Requests\EnomRequest;

final class GetAuthCode extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
    ) {
        //
    }

    protected function command(): string
    {
        return 'SynchAuthInfo';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): AuthCode
    {
        $xml = $response->xml();

        return new AuthCode(
            sld: $this->sld,
            tld: $this->tld,
            code: (string) $xml->authinfo,
        );
    }
}
