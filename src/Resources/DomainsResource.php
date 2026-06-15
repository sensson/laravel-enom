<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\AuthCode;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Dnssec;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Requests\Dnssec\AddDnsSec;
use Sensson\Enom\Requests\Dnssec\DeleteDnsSec;
use Sensson\Enom\Requests\Dnssec\GetDnsSec;
use Sensson\Enom\Requests\Domains\GetAuthCode;
use Sensson\Enom\Requests\Domains\GetDomain;
use Sensson\Enom\Requests\Domains\GetRegLock;
use Sensson\Enom\Requests\Domains\ListDomains;
use Sensson\Enom\Requests\Domains\PushDomain;
use Sensson\Enom\Requests\Domains\RegisterDomain;
use Sensson\Enom\Requests\Domains\RenewDomain;
use Sensson\Enom\Requests\Domains\SetRegLock;
use Sensson\Enom\Requests\Domains\TransferDomain;

class DomainsResource extends BaseResource
{
    /** @return array<string> */
    public function list(): array
    {
        return $this->connector->send(new ListDomains)->dto();
    }

    public function get(string $sld, string $tld): Domain
    {
        $domain = new DomainName($sld, $tld);

        $info = $this->connector->send(new GetDomain($domain))->dto();
        $dnssec = $this->connector->send(new GetDnsSec($domain))->dto();

        return new Domain(
            sld: $info->sld,
            tld: $info->tld,
            status: $info->status,
            expires_at: $info->expires_at,
            dnssec: $dnssec,
        );
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
            new DomainName($sld, $tld), $registrant, $admin, $tech, $billing, $years,
        ))->dto();
    }

    public function renew(string $sld, string $tld, int $years = 1): Domain
    {
        return $this->connector->send(new RenewDomain(new DomainName($sld, $tld), $years))->dto();
    }

    public function transfer(string $sld, string $tld, string $authCode): DomainTransfer
    {
        return $this->connector->send(new TransferDomain(new DomainName($sld, $tld), $authCode))->dto();
    }

    public function sign(string $sld, string $tld, Dnssec $record): void
    {
        $this->connector->send(new AddDnsSec(new DomainName($sld, $tld), $record));
    }

    public function unsign(string $sld, string $tld, Dnssec $record): void
    {
        $this->connector->send(new DeleteDnsSec(new DomainName($sld, $tld), $record));
    }

    public function locked(string $sld, string $tld): bool
    {
        return $this->connector->send(new GetRegLock(new DomainName($sld, $tld)))->dto()->locked;
    }

    public function lock(string $sld, string $tld): void
    {
        $this->connector->send(new SetRegLock(new DomainName($sld, $tld), locked: true));
    }

    public function unlock(string $sld, string $tld): void
    {
        $this->connector->send(new SetRegLock(new DomainName($sld, $tld), locked: false));
    }

    public function getAuthCode(string $sld, string $tld): AuthCode
    {
        return $this->connector->send(new GetAuthCode(new DomainName($sld, $tld)))->dto();
    }

    public function push(string $sld, string $tld, string $account): void
    {
        $this->connector->send(new PushDomain(new DomainName($sld, $tld), $account));
    }

    public function contacts(): ContactResource
    {
        return new ContactResource($this->connector);
    }

    public function nameservers(): NameserverResource
    {
        return new NameserverResource($this->connector);
    }

    public function transfers(): TransferResource
    {
        return new TransferResource($this->connector);
    }
}
