<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Dns;

use Saloon\Http\Response;
use Sensson\Enom\Data\DnsRecord;
use Sensson\Enom\Enums\DnsRecordType;
use Sensson\Enom\Requests\EnomRequest;

final class GetDnsHosts extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetDNSHost';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->sld,
            'TLD' => $this->tld,
        ];
    }

    /** @return array<DnsRecord> */
    public function createDtoFromResponse(Response $response): array
    {
        $xml = $response->xml();
        $records = [];

        foreach ($xml->GetHosts->Host ?? [] as $host) {
            $records[] = new DnsRecord(
                hostname: (string) $host->HostName,
                type: DnsRecordType::from((string) $host->RecordType),
                address: (string) $host->Address,
                ttl: (int) $host->TTL,
                mx_preference: (string) ($host->MXPref ?? '') !== '' ? (int) $host->MXPref : null,
            );
        }

        return $records;
    }
}
