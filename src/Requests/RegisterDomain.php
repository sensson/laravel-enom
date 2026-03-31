<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Sensson\Enom\Data\Contact;

final class RegisterDomain extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $sld,
        protected readonly string $tld,
        protected readonly Contact $registrant,
        protected readonly ?Contact $admin = null,
        protected readonly ?Contact $tech = null,
        protected readonly ?Contact $billing = null,
        protected readonly int $years = 1,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/interface.asp';
    }

    protected function defaultQuery(): array
    {
        $admin = $this->admin ?? $this->registrant;
        $tech = $this->tech ?? $this->registrant;
        $billing = $this->billing ?? $this->registrant;

        return collect([
            'Command' => 'Purchase',
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
}
