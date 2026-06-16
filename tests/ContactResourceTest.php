<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Contacts;
use Sensson\Enom\Enums\Type;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Contacts\GetContacts;
use Sensson\Enom\Requests\Contacts\UpdateContacts;

it('gets contacts for a domain', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <GetContacts>
            <Registrant>
                <RegistrantFirstName>John</RegistrantFirstName>
                <RegistrantLastName>Doe</RegistrantLastName>
                <RegistrantOrganizationName>Acme</RegistrantOrganizationName>
                <RegistrantAddress1>123 Main St</RegistrantAddress1>
                <RegistrantCity>Springfield</RegistrantCity>
                <RegistrantStateProvince>IL</RegistrantStateProvince>
                <RegistrantPostalCode>62701</RegistrantPostalCode>
                <RegistrantCountry>US</RegistrantCountry>
                <RegistrantPhone>+1.5551234567</RegistrantPhone>
                <RegistrantEmailAddress>john@example.com</RegistrantEmailAddress>
            </Registrant>
            <Admin>
                <AdminFirstName>Jane</AdminFirstName>
                <AdminLastName>Doe</AdminLastName>
                <AdminOrganizationName>Acme</AdminOrganizationName>
                <AdminAddress1>456 Oak Ave</AdminAddress1>
                <AdminCity>Springfield</AdminCity>
                <AdminStateProvince>IL</AdminStateProvince>
                <AdminPostalCode>62701</AdminPostalCode>
                <AdminCountry>US</AdminCountry>
                <AdminPhone>+1.5559876543</AdminPhone>
                <AdminEmailAddress>jane@example.com</AdminEmailAddress>
            </Admin>
            <Tech>
                <TechFirstName>John</TechFirstName>
                <TechLastName>Doe</TechLastName>
                <TechOrganizationName>Acme</TechOrganizationName>
                <TechAddress1>123 Main St</TechAddress1>
                <TechCity>Springfield</TechCity>
                <TechStateProvince>IL</TechStateProvince>
                <TechPostalCode>62701</TechPostalCode>
                <TechCountry>US</TechCountry>
                <TechPhone>+1.5551234567</TechPhone>
                <TechEmailAddress>john@example.com</TechEmailAddress>
            </Tech>
            <AuxBilling>
                <AuxBillingFirstName>John</AuxBillingFirstName>
                <AuxBillingLastName>Doe</AuxBillingLastName>
                <AuxBillingOrganizationName>Acme</AuxBillingOrganizationName>
                <AuxBillingAddress1>123 Main St</AuxBillingAddress1>
                <AuxBillingCity>Springfield</AuxBillingCity>
                <AuxBillingStateProvince>IL</AuxBillingStateProvince>
                <AuxBillingPostalCode>62701</AuxBillingPostalCode>
                <AuxBillingCountry>US</AuxBillingCountry>
                <AuxBillingPhone>+1.5551234567</AuxBillingPhone>
                <AuxBillingEmailAddress>john@example.com</AuxBillingEmailAddress>
            </AuxBilling>
        </GetContacts>
    </interface-response>
    XML;

    $mock = new MockClient([
        GetContacts::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->contacts()->get('example', 'com');

    expect($result)
        ->toBeInstanceOf(Contacts::class)
        ->registrant->first_name->toBe('John')
        ->registrant->email->toBe('john@example.com')
        ->admin->first_name->toBe('Jane')
        ->admin->email->toBe('jane@example.com')
        ->tech->first_name->toBe('John')
        ->billing->first_name->toBe('John');

    $mock->assertSent(function (GetContacts $request): bool {
        return $request->query()->get('Command') === 'GetContacts'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('updates a registrant contact', function (): void {
    $mock = new MockClient([
        UpdateContacts::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $contact = new Contact(
        first_name: 'John',
        last_name: 'Doe',
        organization: 'Acme',
        address: '123 Main St',
        city: 'Springfield',
        state: 'IL',
        postal_code: '62701',
        country: 'US',
        phone: '+1.5551234567',
        email: 'john@example.com',
    );

    Enom::domains()->contacts()->update('example', 'com', Type::Registrant, $contact);

    $mock->assertSent(function (UpdateContacts $request): bool {
        return $request->query()->get('Command') === 'Contacts'
            && $request->query()->get('Type') === 'Registrant'
            && $request->query()->get('RegistrantFirstName') === 'John'
            && $request->query()->get('RegistrantEmailAddress') === 'john@example.com';
    });
});

it('updates a tech contact', function (): void {
    $mock = new MockClient([
        UpdateContacts::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $contact = new Contact(
        first_name: 'Jane',
        last_name: 'Smith',
        organization: 'Acme',
        address: '456 Oak Ave',
        city: 'Springfield',
        state: 'IL',
        postal_code: '62701',
        country: 'US',
        phone: '+1.5559876543',
        email: 'jane@example.com',
    );

    Enom::domains()->contacts()->update('example', 'com', Type::Tech, $contact);

    $mock->assertSent(function (UpdateContacts $request): bool {
        return $request->query()->get('Type') === 'Tech'
            && $request->query()->get('TechFirstName') === 'Jane'
            && $request->query()->get('TechEmailAddress') === 'jane@example.com';
    });
});
