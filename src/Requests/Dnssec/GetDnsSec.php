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

        $keyTags = $xml->KeyTag;
        $algorithms = $xml->Algorithm;
        $digestTypes = $xml->DigestType;
        $digests = $xml->Digest;

        $records = [];

        foreach ($keyTags as $index => $keyTag) {
            $records[] = new DnssecRecord(
                key_tag: (int) $keyTag,
                algorithm: (int) ($algorithms[$index] ?? 0),
                digest_type: (int) ($digestTypes[$index] ?? 0),
                digest: (string) ($digests[$index] ?? ''),
            );
        }

        return $records;
    }
}
