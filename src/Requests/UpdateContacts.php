<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Enums\ContactType;

final class UpdateContacts extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $sld,
        protected readonly string $tld,
        protected readonly ContactType $type,
        protected readonly Contact $contact,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/interface.asp';
    }

    protected function defaultQuery(): array
    {
        return collect([
            'Command' => 'Contacts',
            'SLD' => $this->sld,
            'TLD' => $this->tld,
            'ContactType' => $this->type->value,
        ])
            ->merge($this->contact->toQueryParams($this->type->value))
            ->all();
    }
}
