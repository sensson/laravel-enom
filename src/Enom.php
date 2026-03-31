<?php

declare(strict_types=1);

namespace Sensson\Enom;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Sensson\Enom\Resources\DomainResource;

final class Enom extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public function __construct(
        protected readonly string $username,
        protected readonly string $token,
        protected readonly bool $sandbox = true,
    ) {
        //
    }

    public function resolveBaseUrl(): string
    {
        return $this->sandbox
            ? 'https://resellertest.enom.com'
            : 'https://reseller.enom.com';
    }

    protected function defaultQuery(): array
    {
        return [
            'UID' => $this->username,
            'PW' => $this->token,
            'ResponseType' => 'xml',
        ];
    }

    public function domains(): DomainResource
    {
        return new DomainResource($this);
    }
}
