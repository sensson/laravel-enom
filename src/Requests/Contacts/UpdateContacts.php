<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Contacts;

use Sensson\Enom\Data\Contact;
use Sensson\Enom\Enums\ContactType;
use Sensson\Enom\Requests\EnomRequest;

final class UpdateContacts extends EnomRequest
{
    public function __construct(
        private readonly string $sld,
        private readonly string $tld,
        private readonly ContactType $type,
        private readonly Contact $contact,
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
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'ContactType' => $this->type->value,
        ])
            ->merge($this->contact->toQueryParams($this->type->value))
            ->all();
    }
}
