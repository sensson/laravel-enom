<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\AuthCode;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainAvailability;
use Sensson\Enom\Data\DomainLock;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Domains\CheckDomain;
use Sensson\Enom\Requests\Domains\GetAuthCode;
use Sensson\Enom\Requests\Domains\GetDomain;
use Sensson\Enom\Requests\Domains\GetRegLock;
use Sensson\Enom\Requests\Domains\GetRenew;
use Sensson\Enom\Requests\Domains\ListDomains;
use Sensson\Enom\Requests\Domains\PushDomain;
use Sensson\Enom\Requests\Domains\RegisterDomain;
use Sensson\Enom\Requests\Domains\RenewDomain;
use Sensson\Enom\Requests\Domains\SetRegLock;
use Sensson\Enom\Requests\Domains\SetRenew;
use Sensson\Enom\Requests\Domains\TransferDomain;

it('checks domain availability', function (): void {
    $mock = new MockClient([
        CheckDomain::class => MockResponse::make('<interface-response><RRPCode>210</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->check();

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

    $result = Enom::domain('example', 'com')->check();

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

    $result = Enom::domain('example', 'com')->register($contact, years: 2);

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

    Enom::domain('example', 'com')->register($registrant, admin: $admin);

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

    $result = Enom::domain('example', 'com')->get();

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

    $result = Enom::domain('example', 'com')->renew(years: 3);

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

    $result = Enom::domain('example', 'com')->transfer('auth-code-123');

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

it('lists all domains', function (): void {
    $xml = <<<'XML'
    <interface-response>
        <GetAllDomains>
            <DomainDetail><DomainName>example.com</DomainName></DomainDetail>
            <DomainDetail><DomainName>test.nl</DomainName></DomainDetail>
        </GetAllDomains>
    </interface-response>
    XML;

    $mock = new MockClient([
        ListDomains::class => MockResponse::make($xml),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->list();

    expect($result)
        ->toBeArray()
        ->toHaveCount(2)
        ->toContain('example.com')
        ->toContain('test.nl');

    $mock->assertSent(function (ListDomains $request): bool {
        return $request->query()->get('Command') === 'GetAllDomains';
    });
});

it('gets the domain lock status', function (): void {
    $mock = new MockClient([
        GetRegLock::class => MockResponse::make('<interface-response><reg-lock>1</reg-lock></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->getLock();

    expect($result)
        ->toBeInstanceOf(DomainLock::class)
        ->locked->toBeTrue()
        ->name()->toBe('example.com');

    $mock->assertSent(function (GetRegLock $request): bool {
        return $request->query()->get('Command') === 'GetRegLock'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('locks a domain', function (): void {
    $mock = new MockClient([
        SetRegLock::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->lock();

    expect($result)
        ->toBeInstanceOf(DomainLock::class)
        ->locked->toBeTrue();

    $mock->assertSent(function (SetRegLock $request): bool {
        return $request->query()->get('Command') === 'SetRegLock'
            && $request->query()->get('RegLock') === '1';
    });
});

it('unlocks a domain', function (): void {
    $mock = new MockClient([
        SetRegLock::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->unlock();

    expect($result)
        ->toBeInstanceOf(DomainLock::class)
        ->locked->toBeFalse();

    $mock->assertSent(function (SetRegLock $request): bool {
        return $request->query()->get('Command') === 'SetRegLock'
            && $request->query()->get('RegLock') === '0';
    });
});

it('gets auto renew status', function (): void {
    $mock = new MockClient([
        GetRenew::class => MockResponse::make('<interface-response><auto_renew>1</auto_renew></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->getAutoRenew();

    expect($result)->toBeTrue();

    $mock->assertSent(function (GetRenew $request): bool {
        return $request->query()->get('Command') === 'GetRenew'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('sets auto renew', function (): void {
    $mock = new MockClient([
        SetRenew::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::domain('example', 'com')->setAutoRenew(true);

    $mock->assertSent(function (SetRenew $request): bool {
        return $request->query()->get('Command') === 'SetRenew'
            && $request->query()->get('AutoRenew') === '1';
    });
});

it('gets the auth code', function (): void {
    $mock = new MockClient([
        GetAuthCode::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode><authinfo>EPP-SECRET-123</authinfo></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domain('example', 'com')->getAuthCode();

    expect($result)
        ->toBeInstanceOf(AuthCode::class)
        ->code->toBe('EPP-SECRET-123')
        ->name()->toBe('example.com');

    $mock->assertSent(function (GetAuthCode $request): bool {
        return $request->query()->get('Command') === 'SynchAuthInfo'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com';
    });
});

it('pushes a domain to another account', function (): void {
    $mock = new MockClient([
        PushDomain::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::domain('example', 'com')->push('other-reseller');

    $mock->assertSent(function (PushDomain $request): bool {
        return $request->query()->get('Command') === 'PushDomain'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('AccountName') === 'other-reseller';
    });
});
