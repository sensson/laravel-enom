<?php

declare(strict_types=1);

namespace Sensson\Enom\Resources;

use Saloon\Http\BaseResource;
use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Contacts;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Enums\Type;
use Sensson\Enom\Requests\Contacts\GetContacts;
use Sensson\Enom\Requests\Contacts\UpdateContacts;

class ContactResource extends BaseResource
{
    public function get(string $sld, string $tld): Contacts
    {
        return $this->connector->send(new GetContacts(new DomainName($sld, $tld)))->dto();
    }

    public function update(string $sld, string $tld, Type $type, Contact $contact, bool $optOutOfTransferLock = false): Response
    {
        return $this->connector->send(new UpdateContacts(
            domain: new DomainName($sld, $tld),
            type: $type,
            contact: $contact,
            optOutOfTransferLock: $optOutOfTransferLock,
        ));
    }
}
