<?php

declare(strict_types=1);

namespace Sensson\Enom;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Sensson\Enom\Data\EnomCredentials;
use Sensson\Enom\Resources\AccountResource;
use Sensson\Enom\Resources\DomainResource;
use Sensson\Enom\Resources\TransferResource;

final class Enom extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public function __construct(
        protected readonly EnomCredentials $credentials,
    ) {
        //
    }

    public function resolveBaseUrl(): string
    {
        return $this->credentials->sandbox
            ? 'https://resellertest.enom.com'
            : 'https://reseller.enom.com';
    }

    protected function defaultQuery(): array
    {
        return [
            'UID' => $this->credentials->username,
            'PW' => $this->credentials->token,
            'ResponseType' => 'xml',
        ];
    }

    public function domains(): DomainResource
    {
        return new DomainResource($this);
    }

    public function transfers(): TransferResource
    {
        return new TransferResource($this);
    }

    public function account(): AccountResource
    {
        return new AccountResource($this);
    }
}
