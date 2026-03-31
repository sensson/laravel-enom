<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Requests\CheckDomain;
use Sensson\Enom\Requests\GetDomain;
use Sensson\Enom\Requests\RegisterDomain;
use Sensson\Enom\Requests\RenewDomain;
use Sensson\Enom\Requests\TransferDomain;

final class DomainResource extends BaseResource
{
    public function check(string $sld, string $tld): Response
    {
        return $this->connector->send(new CheckDomain($sld, $tld));
    }

    public function get(string $sld, string $tld): Response
    {
        return $this->connector->send(new GetDomain($sld, $tld));
    }

    public function register(
        string $sld,
        string $tld,
        Contact $registrant,
        ?Contact $admin = null,
        ?Contact $tech = null,
        ?Contact $billing = null,
        int $years = 1,
    ): Response {
        return $this->connector->send(new RegisterDomain(
            $sld, $tld, $registrant, $admin, $tech, $billing, $years,
        ));
    }

    public function renew(string $sld, string $tld, int $years = 1): Response
    {
        return $this->connector->send(new RenewDomain($sld, $tld, $years));
    }

    public function transfer(string $sld, string $tld, string $code): Response
    {
        return $this->connector->send(new TransferDomain($sld, $tld, $code));
    }

    public function contacts(string $sld, string $tld): ContactResource
    {
        return new ContactResource($this->connector, $sld, $tld);
    }
}
