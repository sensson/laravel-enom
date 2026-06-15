<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\AuthCode;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class GetAuthCode extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
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
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): AuthCode
    {
        $xml = $response->xml();

        return new AuthCode(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            code: (string) $xml->authinfo,
        );
    }
}
