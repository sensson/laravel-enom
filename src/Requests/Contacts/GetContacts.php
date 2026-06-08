<?php

declare(strict_types=1);

namespace Sensson\Enom\Requests\Contacts;

use Saloon\Http\Response;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Contacts;
use Sensson\Enom\Data\DomainName;
use Sensson\Enom\Requests\EnomRequest;
use SimpleXMLElement;

final class GetContacts extends EnomRequest
{
    public function __construct(
        private readonly DomainName $domain,
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

    private function parseContact(SimpleXMLElement $node): Contact
    {
        return new Contact(
            first_name: (string) $node->FirstName,
            last_name: (string) $node->LastName,
            organization: (string) $node->OrganizationName,
            address: (string) $node->Address1,
            city: (string) $node->City,
            state: (string) $node->StateProvince,
            postal_code: (string) $node->PostalCode,
            country: (string) $node->Country,
            phone: (string) $node->Phone,
            email: (string) $node->EmailAddress,
            address_2: (string) ($node->Address2 ?? null) ?: null,
            fax: (string) ($node->Fax ?? null) ?: null,
        );
    }
}
