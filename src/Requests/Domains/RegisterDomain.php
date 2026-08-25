<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Data\Nameserver;
use Sensson\Enom\Requests\EnomRequest;

class RegisterDomain extends EnomRequest
{
    /**
     * @param  array<Nameserver>  $nameservers
     */
    public function __construct(
        protected DomainName $domain,
        protected Contact $registrant,
        protected ?Contact $admin = null,
        protected ?Contact $tech = null,
        protected ?Contact $billing = null,
        protected int $years = 1,
        protected array $nameservers = [],
    ) {
        //
    }

    protected function command(): string
    {
        return 'Purchase';
    }

    protected function parameters(): array
    {
        $admin = $this->admin ?? $this->registrant;
        $tech = $this->tech ?? $this->registrant;
        $billing = $this->billing ?? $this->registrant;

        $nameservers = collect(array_values($this->nameservers))
            ->mapWithKeys(fn (Nameserver $nameserver, int $index): array => [
                'NS'.($index + 1) => $nameserver->host,
            ])
            ->whenEmpty(fn ($parameters) => $parameters->put('UseDNS', 'default'));

        return collect([
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'NumYears' => $this->years,
        ])
            ->merge($this->registrant->toQueryParams('Registrant'))
            ->merge($admin->toQueryParams('Admin'))
            ->merge($tech->toQueryParams('Tech'))
            ->merge($billing->toQueryParams('AuxBilling'))
            ->merge($nameservers)
            ->all();
    }

    public function createDtoFromResponse(Response $response): Domain
    {
        return new Domain(
            sld: $this->domain->sld,
            tld: $this->domain->tld,
            nameservers: $this->nameservers,
        );
    }
}
