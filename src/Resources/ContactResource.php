<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Contacts;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Enums\ContactType;
use Sensson\Enom\Requests\Contacts\GetContacts;
use Sensson\Enom\Requests\Contacts\UpdateContacts;

final class ContactResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
        protected readonly DomainName $domain,
    ) {
        //
    }

    public function get(): Contacts
    {
        return $this->connector->send(new GetContacts($this->domain))->dto();
    }

    public function update(ContactType $type, Contact $contact): Response
    {
        return $this->connector->send(new UpdateContacts($this->domain, $type, $contact));
    }
}
