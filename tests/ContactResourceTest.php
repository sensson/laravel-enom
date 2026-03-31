<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Enums\ContactType;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\GetContacts;
use Sensson\Enom\Requests\UpdateContacts;

it('gets contacts for a domain', function (): void {
    $mock = new MockClient([
        GetContacts::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::domains()->contacts('example', 'com')->get();

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

    Enom::domains()->contacts('example', 'com')->update(ContactType::Registrant, $contact);

    $mock->assertSent(function (UpdateContacts $request): bool {
        return $request->query()->get('Command') === 'Contacts'
            && $request->query()->get('ContactType') === 'Registrant'
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

    Enom::domains()->contacts('example', 'com')->update(ContactType::Tech, $contact);

    $mock->assertSent(function (UpdateContacts $request): bool {
        return $request->query()->get('ContactType') === 'Tech'
            && $request->query()->get('TechFirstName') === 'Jane'
            && $request->query()->get('TechEmailAddress') === 'jane@example.com';
    });
});
