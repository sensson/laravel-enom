<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\Nameserver;
use Sensson\Enom\Requests\Nameservers\GetNameservers;
use Sensson\Enom\Requests\Nameservers\UpdateNameservers;

class NameserverResource extends BaseResource
{
    /**
     * @return array<Nameserver>
     */
    public function get(string $sld, string $tld): array
    {
        return $this->connector->send(new GetNameservers(new DomainName($sld, $tld)))->dto();
    }

    /**
     * @param  array<Nameserver>  $nameservers
     * @return array<Nameserver>
     */
    public function update(string $sld, string $tld, array $nameservers): array
    {
        return $this->connector->send(new UpdateNameservers(new DomainName($sld, $tld), $nameservers))->dto();
    }
}
