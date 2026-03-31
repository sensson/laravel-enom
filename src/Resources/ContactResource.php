<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Enums\ContactType;
use Sensson\Enom\Requests\GetContacts;
use Sensson\Enom\Requests\UpdateContacts;

final class ContactResource extends BaseResource
{
    public function __construct(
        protected readonly Connector $connector,
        protected readonly string $sld,
        protected readonly string $tld,
    ) {
        //
    }

    public function get(): Response
    {
        return $this->connector->send(new GetContacts($this->sld, $this->tld));
    }

    public function update(ContactType $type, Contact $contact): Response
    {
        return $this->connector->send(new UpdateContacts($this->sld, $this->tld, $type, $contact));
    }
}
