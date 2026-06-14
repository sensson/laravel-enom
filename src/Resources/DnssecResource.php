<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Enom\Data\DnssecRecord;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\Dnssec\AddDnsSec;
use Sensson\Enom\Requests\Dnssec\DeleteDnsSec;
use Sensson\Enom\Requests\Dnssec\GetDnsSec;

final class DnssecResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
        protected readonly DomainName $domain,
    ) {
        //
    }

    /** @return array<DnssecRecord> */
    public function get(): array
    {
        return $this->connector->send(new GetDnsSec($this->domain))->dto();
    }

    public function add(DnssecRecord $record): DnssecRecord
    {
        return $this->connector->send(new AddDnsSec($this->domain, $record))->dto();
    }

    public function remove(DnssecRecord $record): void
    {
        $this->connector->send(new DeleteDnsSec($this->domain, $record));
    }
}
