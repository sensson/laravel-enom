<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainAvailability;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\CheckDomain;
use Sensson\Enom\Requests\GetDomain;
use Sensson\Enom\Requests\RegisterDomain;
use Sensson\Enom\Requests\RenewDomain;
use Sensson\Enom\Requests\TransferDomain;

it('checks domain availability', function (): void {
    $mock = new MockClient([
        CheckDomain::class => MockResponse::make('<interface-response><RRPCode>210</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->check('example', 'com');

    expect($result)
        ->toBeInstanceOf(DomainAvailability::class)
        ->available->toBeTrue()
        ->sld->toBe('example')
        ->tld->toBe('com')
        ->name()->toBe('example.com');

    $mock->assertSent(function (CheckDomain $request): bool {
        return $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('Command') === 'Check';
    });
});

it('checks unavailable domain', function (): void {
    $mock = new MockClient([
        CheckDomain::class => MockResponse::make('<interface-response><RRPCode>211</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->check('example', 'com');

    expect($result)
        ->toBeInstanceOf(DomainAvailability::class)
        ->available->toBeFalse();
});

it('registers a domain', function (): void {
    $mock = new MockClient([
        RegisterDomain::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
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

    $result = Enom::domains()->register('example', 'com', $contact, years: 2);

    expect($result)
        ->toBeInstanceOf(Domain::class)
        ->sld->toBe('example')
        ->tld->toBe('com');

    $mock->assertSent(function (RegisterDomain $request): bool {
        return $request->query()->get('Command') === 'Purchase'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('NumYears') === 2
            && $request->query()->get('RegistrantFirstName') === 'John'
            && $request->query()->get('AdminFirstName') === 'John'
            && $request->query()->get('TechFirstName') === 'John'
            && $request->query()->get('AuxBillingFirstName') === 'John';
    });
});

it('registers a domain with separate contacts', function (): void {
    $mock = new MockClient([
        RegisterDomain::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $registrant = new Contact(
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

    $admin = new Contact(
        first_name: 'Jane',
        last_name: 'Doe',
        organization: 'Acme',
        address: '456 Oak Ave',
        city: 'Springfield',
        state: 'IL',
        postal_code: '62701',
        country: 'US',
        phone: '+1.5559876543',
        email: 'jane@example.com',
    );

    Enom::domains()->register('example', 'com', $registrant, admin: $admin);

    $mock->assertSent(function (RegisterDomain $request): bool {
        return $request->query()->get('RegistrantFirstName') === 'John'
            && $request->query()->get('AdminFirstName') === 'Jane'
            && $request->query()->get('TechFirstName') === 'John'
            && $request->query()->get('AuxBillingFirstName') === 'John';
    });
});

it('gets domain info', function (): void {
    $mock = new MockClient([
        GetDomain::class => MockResponse::make(
            '<interface-response><GetDomainInfo><status>Registered</status><expiration-date>2026-01-01</expiration-date></GetDomainInfo></interface-response>'
        ),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->get('example', 'com');

    expect($result)
        ->toBeInstanceOf(Domain::class)
        ->sld->toBe('example')
        ->tld->toBe('com')
        ->status->toBe('Registered')
        ->expiration->toBe('2026-01-01')
        ->name()->toBe('example.com');

    $mock->assertSent(function (GetDomain $request): bool {
        return $request->query()->get('Command') === 'GetDomainInfo'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('renews a domain', function (): void {
    $mock = new MockClient([
        RenewDomain::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->renew('example', 'com', years: 3);

    expect($result)
        ->toBeInstanceOf(Domain::class)
        ->sld->toBe('example')
        ->tld->toBe('com');

    $mock->assertSent(function (RenewDomain $request): bool {
        return $request->query()->get('Command') === 'Extend'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('NumYears') === 3;
    });
});

it('transfers a domain', function (): void {
    $mock = new MockClient([
        TransferDomain::class => MockResponse::make(
            '<interface-response><OrderID>12345</OrderID><TransferOrder><statusid>1</statusid></TransferOrder></interface-response>'
        ),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->transfer('example', 'com', 'auth-code-123');

    expect($result)
        ->toBeInstanceOf(DomainTransfer::class)
        ->sld->toBe('example')
        ->tld->toBe('com')
        ->order_id->toBe('12345')
        ->status_id->toBe('1');

    $mock->assertSent(function (TransferDomain $request): bool {
        return $request->query()->get('Command') === 'TP_CreateOrder'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('DomainPassword') === 'auth-code-123';
    });
});
