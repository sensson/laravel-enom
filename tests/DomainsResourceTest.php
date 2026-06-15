<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Data\AuthCode;
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Data\Dnssec;
use Sensson\Enom\Data\Domain;
use Sensson\Enom\Data\DomainTransfer;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Dnssec\AddDnsSec;
use Sensson\Enom\Requests\Dnssec\DeleteDnsSec;
use Sensson\Enom\Requests\Dnssec\GetDnsSec;
use Sensson\Enom\Requests\Domains\GetAuthCode;
use Sensson\Enom\Requests\Domains\GetDomain;
use Sensson\Enom\Requests\Domains\GetRegLock;
use Sensson\Enom\Requests\Domains\ListDomains;
use Sensson\Enom\Requests\Domains\PushDomain;
use Sensson\Enom\Requests\Domains\RegisterDomain;
use Sensson\Enom\Requests\Domains\RenewDomain;
use Sensson\Enom\Requests\Domains\SetRegLock;
use Sensson\Enom\Requests\Domains\TransferDomain;

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
            && $request->query()->get('AdminFirstName') === 'John';
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
            && $request->query()->get('TechFirstName') === 'John';
    });
});

it('gets domain info including dnssec', function (): void {
    $mock = new MockClient([
        GetDomain::class => MockResponse::make(
            '<interface-response><GetDomainInfo><status>Registered</status><expiration-date>2026-01-01</expiration-date></GetDomainInfo></interface-response>'
        ),
        GetDnsSec::class => MockResponse::make(
            '<interface-response><DnsSecData><KeyData><KeyTag>12345</KeyTag><Algorithm>8</Algorithm><DigestType>2</DigestType><Digest>ABC123</Digest></KeyData></DnsSecData></interface-response>'
        ),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->get('example', 'com');

    expect($result)
        ->toBeInstanceOf(Domain::class)
        ->sld->toBe('example')
        ->tld->toBe('com')
        ->status->toBe('Registered')
        ->expires_at->toBe('2026-01-01')
        ->dnssec->toHaveCount(1);

    expect($result->dnssec[0])
        ->toBeInstanceOf(Dnssec::class)
        ->key_tag->toBe(12345);
});

it('renews a domain', function (): void {
    $mock = new MockClient([
        RenewDomain::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $result = Enom::domains()->renew('example', 'com', years: 3);

    expect($result)->toBeInstanceOf(Domain::class);

    $mock->assertSent(function (RenewDomain $request): bool {
        return $request->query()->get('Command') === 'Extend'
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
        ->name()->toBe('example.com')
        ->status->toBe('1');

    $mock->assertSent(function (TransferDomain $request): bool {
        return $request->query()->get('Command') === 'TP_CreateOrder'
            && $request->query()->get('DomainPassword') === 'auth-code-123';
    });
});

it('signs a domain with a dnssec record', function (): void {
    $mock = new MockClient([
        AddDnsSec::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $record = new Dnssec(key_tag: 12345, algorithm: 8, digest_type: 2, digest: 'ABC123');

    Enom::domains()->sign('example', 'com', $record);

    $mock->assertSent(function (AddDnsSec $request): bool {
        return $request->query()->get('Command') === 'AddDnsSec'
            && $request->query()->get('SLD') === 'example'
            && $request->query()->get('TLD') === 'com'
            && $request->query()->get('KeyTag') === 12345;
    });
});

it('unsigns a domain by removing a dnssec record', function (): void {
    $mock = new MockClient([
        DeleteDnsSec::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    $record = new Dnssec(key_tag: 12345, algorithm: 8, digest_type: 2, digest: 'ABC123');

    Enom::domains()->unsign('example', 'com', $record);

    $mock->assertSent(function (DeleteDnsSec $request): bool {
        return $request->query()->get('Command') === 'DeleteDnsSec'
            && $request->query()->get('KeyTag') === 12345;
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

    expect(Enom::domains()->list())
        ->toBeArray()
        ->toHaveCount(2)
        ->toContain('example.com');
});

it('gets the lock status', function (): void {
    $mock = new MockClient([
        GetRegLock::class => MockResponse::make('<interface-response><reg-lock>1</reg-lock></interface-response>'),
    ]);

    Enom::fake($mock);

    expect(Enom::domains()->locked('example', 'com'))->toBeTrue();

    $mock->assertSent(function (GetRegLock $request): bool {
        return $request->query()->get('Command') === 'GetRegLock'
            && $request->query()->get('SLD') === 'example';
    });
});

it('locks a domain', function (): void {
    $mock = new MockClient([
        SetRegLock::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::domains()->lock('example', 'com');

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

    Enom::domains()->unlock('example', 'com');

    $mock->assertSent(function (SetRegLock $request): bool {
        return $request->query()->get('RegLock') === '0';
    });
});

it('gets the auth code', function (): void {
    $mock = new MockClient([
        GetAuthCode::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode><authinfo>EPP-SECRET-123</authinfo></interface-response>'),
    ]);

    Enom::fake($mock);

    expect(Enom::domains()->getAuthCode('example', 'com'))
        ->toBeInstanceOf(AuthCode::class)
        ->code->toBe('EPP-SECRET-123');
});

it('pushes a domain to another account', function (): void {
    $mock = new MockClient([
        PushDomain::class => MockResponse::make('<interface-response><RRPCode>200</RRPCode></interface-response>'),
    ]);

    Enom::fake($mock);

    Enom::domains()->push('example', 'com', 'other-reseller');

    $mock->assertSent(function (PushDomain $request): bool {
        return $request->query()->get('Command') === 'PushDomain'
            && $request->query()->get('AccountName') === 'other-reseller';
    });
});
