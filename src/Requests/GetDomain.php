<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Sensson\Enom\Data\Domain;

final class GetDomain extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $sld,
        protected readonly string $tld,
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
            'Command' => 'GetDomainInfo',
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
