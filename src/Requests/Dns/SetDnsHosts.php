<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Dns;

use Saloon\Http\Response;
use Sensson\Enom\Data\DnsRecord;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;

class SetDnsHosts extends EnomRequest
{
    /** @param array<DnsRecord> $records */
    public function __construct(
        private readonly DomainName $domain,
        private readonly array $records,
    ) {
        //
    }

    protected function command(): string
    {
        return 'SetDNSHost';
    }

    protected function parameters(): array
    {
        $params = [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];

        foreach ($this->records as $index => $record) {
            $position = $index + 1;
            $params["HostName{$position}"] = $record->hostname;
            $params["RecordType{$position}"] = $record->type->value;
            $params["Address{$position}"] = $record->address;
            $params["TTL{$position}"] = $record->ttl;

            if ($record->mx_preference !== null) {
                $params["MXPref{$position}"] = $record->mx_preference;
            }
        }

        return $params;
    }

    /** @return array<DnsRecord> */
    public function createDtoFromResponse(Response $response): array
    {
        return $this->records;
    }
}
