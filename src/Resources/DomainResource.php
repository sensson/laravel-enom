<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainAvailability;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Requests\CheckDomain;
use Sensson\Enom\Requests\GetDomain;
use Sensson\Enom\Requests\RegisterDomain;
use Sensson\Enom\Requests\RenewDomain;
use Sensson\Enom\Requests\TransferDomain;

final class DomainResource extends BaseResource
{
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

    public function contacts(string $sld, string $tld): ContactResource
    {
        return new ContactResource($this->connector, $sld, $tld);
    }
}
