<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Dnssec;

use Saloon\Http\Response;
use Sensson\Enom\Data\DnssecRecord;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

final class GetDnsSec extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetDnsSec';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    /** @return array<DnssecRecord> */
    public function createDtoFromResponse(Response $response): array
    {
        $xml = $response->xml();

        $records = [];

        foreach ($xml->DnsSecData->KeyData ?? [] as $key) {
            $records[] = new DnssecRecord(
                key_tag: (int) $key->KeyTag,
                algorithm: (int) $key->Algorithm,
                digest_type: (int) $key->DigestType,
                digest: (string) $key->Digest,
            );
        }

        return $records;
    }
}
