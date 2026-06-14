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

        $index = 0;

        foreach ($xml->KeyTag as $keyTag) {
            $records[] = new DnssecRecord(
                key_tag: (int) $keyTag,
                algorithm: (int) ($xml->Algorithm[$index] ?? 0),
                digest_type: (int) ($xml->DigestType[$index] ?? 0),
                digest: (string) ($xml->Digest[$index] ?? ''),
            );

            $index++;
        }

        return $records;
    }
}
