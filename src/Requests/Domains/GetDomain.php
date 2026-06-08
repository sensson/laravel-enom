<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Requests\EnomRequest;

final class GetDomain extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetDomainInfo';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): Domain
    {
        $xml = $response->xml();
        $info = $xml->GetDomainInfo;

        return new Domain(
            sld: $this->sld,
            tld: $this->tld,
            status: (string) ($info->status ?? null) ?: null,
            expiration: (string) ($info->{'expiration-date'} ?? null) ?: null,
            auto_renew: isset($info->services)
                ? str((string) $info->services->entry->value)->lower()->toString() === 'yes'
                : null,
        );
    }
}
