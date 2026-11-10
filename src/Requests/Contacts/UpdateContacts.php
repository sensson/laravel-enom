<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Contacts;

use Illuminate\Support\Collection;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Enums\Type;
use Sensson\Enom\Requests\EnomRequest;

class UpdateContacts extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
        protected Type $type,
        protected Contact $contact,
        protected bool $optOutOfTransferLock = false,
    ) {
        //
    }

    protected function command(): string
    {
        return 'Contacts';
    }

    protected function parameters(): array
    {
        return collect([
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
            'Type' => $this->type->value,
        ])
            ->merge($this->contact->toQueryParams($this->type->value))
            ->when(
                $this->optOutOfTransferLock && $this->type === Type::Registrant,
                fn (Collection $parameters): Collection => $parameters->put('IRTPOptOut', 'True'),
            )
            ->all();
    }
}
