<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Requests\Domains\ListDomains;

final class DomainsResource extends BaseResource
{
    /** @return array<string> */
    public function list(): array
    {
        return $this->connector->send(new ListDomains)->dto();
    }
}
