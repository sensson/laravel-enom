<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\Nameservers;
use Sensson\Enom\Requests\Nameservers\DeleteNameserver;
use Sensson\Enom\Requests\Nameservers\GetNameservers;
use Sensson\Enom\Requests\Nameservers\RegisterNameserver;
use Sensson\Enom\Requests\Nameservers\UpdateNameservers;

final class NameserverResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
        protected readonly DomainName $domain,
    ) {
        //
    }

    public function get(): Nameservers
    {
        return $this->connector->send(new GetNameservers($this->domain))->dto();
    }

    public function update(Nameservers $nameservers): Nameservers
    {
        return $this->connector->send(new UpdateNameservers($this->domain, $nameservers))->dto();
    }

    public function register(string $nameserver, string $ip): void
    {
        $this->connector->send(new RegisterNameserver($nameserver, $ip));
    }

    public function delete(string $nameserver): void
    {
        $this->connector->send(new DeleteNameserver($nameserver));
    }
}
