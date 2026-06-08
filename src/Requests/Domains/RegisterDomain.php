<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Domains;

use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Requests\EnomRequest;

final class RegisterDomain extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
        private readonly Contact $registrant,
        private readonly ?Contact $admin = null,
        private readonly ?Contact $tech = null,
        private readonly ?Contact $billing = null,
        private readonly int $years = 1,
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

        return collect([
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'NumYears' => $this->years,
        ])
            ->merge($this->registrant->toQueryParams('Registrant'))
            ->merge($admin->toQueryParams('Admin'))
            ->merge($tech->toQueryParams('Tech'))
            ->merge($billing->toQueryParams('AuxBilling'))
            ->all();
    }

    public function createDtoFromResponse(Response $response): Domain
    {
        return new Domain(
            sld: $this->sld,
            tld: $this->tld,
        );
    }
}
