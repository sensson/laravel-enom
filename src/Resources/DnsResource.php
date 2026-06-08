<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Enom\Data\DnsRecord;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\Dns\GetDnsHosts;
use Sensson\Enom\Requests\Dns\SetDnsHosts;

final class DnsResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
        protected readonly DomainName $domain,
    ) {
        //
    }

    /** @return array<DnsRecord> */
    public function get(): array
    {
        return $this->connector->send(new GetDnsHosts($this->domain))->dto();
    }

    /** @param array<DnsRecord> $records */
    public function update(array $records): array
    {
        return $this->connector->send(new SetDnsHosts($this->domain, $records))->dto();
    }
}
