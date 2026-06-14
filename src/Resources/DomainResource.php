<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Sensson\Enom\Data\AuthCode;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainAvailability;
use Sensson\Enom\Data\DomainLock;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Requests\Domains\CheckDomain;
use Sensson\Enom\Requests\Domains\GetAuthCode;
use Sensson\Enom\Requests\Domains\GetDomain;
use Sensson\Enom\Requests\Domains\GetRegLock;
use Sensson\Enom\Requests\Domains\GetRenew;
use Sensson\Enom\Requests\Domains\PushDomain;
use Sensson\Enom\Requests\Domains\RegisterDomain;
use Sensson\Enom\Requests\Domains\RenewDomain;
use Sensson\Enom\Requests\Domains\SetRegLock;
use Sensson\Enom\Requests\Domains\SetRenew;
use Sensson\Enom\Requests\Domains\TransferDomain;

final class DomainResource extends BaseResource
{
    private DomainName $domain;

    public function __construct(
        protected readonly Connector $connector,
        string $sld,
        string $tld,
    ) {
        $this->domain = new DomainName($sld, $tld);
    }

    public function check(): DomainAvailability
    {
        return $this->connector->send(new CheckDomain($this->domain))->dto();
    }

    public function get(): Domain
    {
        return $this->connector->send(new GetDomain($this->domain))->dto();
    }

    public function register(
        Contact $registrant,
        ?Contact $admin = null,
        ?Contact $tech = null,
        ?Contact $billing = null,
        int $years = 1,
    ): Domain {
        return $this->connector->send(new RegisterDomain(
            $this->domain, $registrant, $admin, $tech, $billing, $years,
        ))->dto();
    }

    public function renew(int $years = 1): Domain
    {
        return $this->connector->send(new RenewDomain($this->domain, $years))->dto();
    }

    public function transfer(string $code): DomainTransfer
    {
        return $this->connector->send(new TransferDomain($this->domain, $code))->dto();
    }

    public function getLock(): DomainLock
    {
        return $this->connector->send(new GetRegLock($this->domain))->dto();
    }

    public function lock(): DomainLock
    {
        return $this->connector->send(new SetRegLock($this->domain, locked: true))->dto();
    }

    public function unlock(): DomainLock
    {
        return $this->connector->send(new SetRegLock($this->domain, locked: false))->dto();
    }

    public function getAutoRenew(): bool
    {
        return $this->connector->send(new GetRenew($this->domain))->dto();
    }

    public function setAutoRenew(bool $enabled): void
    {
        $this->connector->send(new SetRenew($this->domain, $enabled));
    }

    public function getAuthCode(): AuthCode
    {
        return $this->connector->send(new GetAuthCode($this->domain))->dto();
    }

    public function push(string $account): void
    {
        $this->connector->send(new PushDomain($this->domain, $account));
    }

    public function contacts(): ContactResource
    {
        return new ContactResource($this->connector, $this->domain);
    }

    public function nameservers(): NameserverResource
    {
        return new NameserverResource($this->connector, $this->domain);
    }

    public function dns(): DnsResource
    {
        return new DnsResource($this->connector, $this->domain);
    }

    public function dnssec(): DnssecResource
    {
        return new DnssecResource($this->connector, $this->domain);
    }

    public function transfers(): DomainTransferResource
    {
        return new DomainTransferResource($this->connector, $this->domain);
    }
}
