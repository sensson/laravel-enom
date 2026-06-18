<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Contacts;

use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Contacts;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;
use SimpleXMLElement;

class GetContacts extends EnomRequest
{
    public function __construct(
        protected DomainName $domain,
    ) {
        //
    }

    protected function command(): string
    {
        return 'GetContacts';
    }

    protected function parameters(): array
    {
        return [
            'SLD' => $this->domain->sld,
            'TLD' => $this->domain->tld,
        ];
    }

    public function createDtoFromResponse(Response $response): Contacts
    {
        $xml = $response->xml()->GetContacts;

        return new Contacts(
            registrant: $this->parseContact($xml->Registrant),
            admin: $this->parseContact($xml->Admin),
            tech: $this->parseContact($xml->Tech),
            billing: $this->parseContact($xml->AuxBilling),
        );
    }

    protected function parseContact(SimpleXMLElement $node): Contact
    {
        $prefix = $node->getName();

        return new Contact(
            first_name: (string) $node->{"{$prefix}FirstName"},
            last_name: (string) $node->{"{$prefix}LastName"},
            organization: (string) $node->{"{$prefix}OrganizationName"},
            address: (string) $node->{"{$prefix}Address1"},
            city: (string) $node->{"{$prefix}City"},
            state: (string) $node->{"{$prefix}StateProvince"},
            postal_code: (string) $node->{"{$prefix}PostalCode"},
            country: (string) $node->{"{$prefix}Country"},
            phone: (string) $node->{"{$prefix}Phone"},
            email: (string) $node->{"{$prefix}EmailAddress"},
            address_2: (string) ($node->{"{$prefix}Address2"} ?? null) ?: null,
            fax: (string) ($node->{"{$prefix}Fax"} ?? null) ?: null,
        );
    }
}
