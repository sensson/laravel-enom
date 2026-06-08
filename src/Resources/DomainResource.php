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
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Requests\Domains\CheckDomain;
use Sensson\Enom\Requests\Domains\GetAuthCode;
use Sensson\Enom\Requests\Domains\GetDomain;
use Sensson\Enom\Requests\Domains\GetRegLock;
use Sensson\Enom\Requests\Domains\GetRenew;
use Sensson\Enom\Requests\Domains\ListDomains;
use Sensson\Enom\Requests\Domains\PushDomain;
use Sensson\Enom\Requests\Domains\RegisterDomain;
use Sensson\Enom\Requests\Domains\RenewDomain;
use Sensson\Enom\Requests\Domains\SetRegLock;
use Sensson\Enom\Requests\Domains\SetRenew;
use Sensson\Enom\Requests\Domains\TransferDomain;

final class DomainResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
    ) {
        //
    }

    public function check(string $sld, string $tld): DomainAvailability
    {
        return $this->connector->send(new CheckDomain($sld, $tld))->dto();
    }

    public function get(string $sld, string $tld): Domain
    {
        return $this->connector->send(new GetDomain($sld, $tld))->dto();
    }

    public function register(
        string $sld,
        string $tld,
        Contact $registrant,
        ?Contact $admin = null,
        ?Contact $tech = null,
        ?Contact $billing = null,
        int $years = 1,
    ): Domain {
        return $this->connector->send(new RegisterDomain(
            $sld, $tld, $registrant, $admin, $tech, $billing, $years,
        ))->dto();
    }

    public function renew(string $sld, string $tld, int $years = 1): Domain
    {
        return $this->connector->send(new RenewDomain($sld, $tld, $years))->dto();
    }

    public function transfer(string $sld, string $tld, string $code): DomainTransfer
    {
        return $this->connector->send(new TransferDomain($sld, $tld, $code))->dto();
    }

    /** @return array<string> */
    public function list(): array
    {
        return $this->connector->send(new ListDomains)->dto();
    }

    public function getLock(string $sld, string $tld): DomainLock
    {
        return $this->connector->send(new GetRegLock($sld, $tld))->dto();
    }

    public function lock(string $sld, string $tld): DomainLock
    {
        return $this->connector->send(new SetRegLock($sld, $tld, locked: true))->dto();
    }

    public function unlock(string $sld, string $tld): DomainLock
    {
        return $this->connector->send(new SetRegLock($sld, $tld, locked: false))->dto();
    }

    public function getAutoRenew(string $sld, string $tld): bool
    {
        return $this->connector->send(new GetRenew($sld, $tld))->dto();
    }

    public function setAutoRenew(string $sld, string $tld, bool $enabled): void
    {
        $this->connector->send(new SetRenew($sld, $tld, $enabled));
    }

    public function getAuthCode(string $sld, string $tld): AuthCode
    {
        return $this->connector->send(new GetAuthCode($sld, $tld))->dto();
    }

    public function push(string $sld, string $tld, string $account): void
    {
        $this->connector->send(new PushDomain($sld, $tld, $account));
    }

    public function contacts(string $sld, string $tld): ContactResource
    {
        return new ContactResource($this->connector, $sld, $tld);
    }

    public function nameservers(string $sld, string $tld): NameserverResource
    {
        return new NameserverResource($this->connector, $sld, $tld);
    }

    public function dns(string $sld, string $tld): DnsResource
    {
        return new DnsResource($this->connector, $sld, $tld);
    }
}
