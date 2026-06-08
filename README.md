# Laravel Enom

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sensson/laravel-enom.svg?style=flat-square)](https://packagist.org/packages/sensson/laravel-enom)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sensson/laravel-enom/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sensson/laravel-enom/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sensson/laravel-enom/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sensson/laravel-enom/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sensson/laravel-enom.svg?style=flat-square)](https://packagist.org/packages/sensson/laravel-enom)

A Laravel package for the Enom domain reseller API, built on [SaloonPHP](https://docs.saloon.dev).

## Installation

```bash
composer require sensson/laravel-enom
```

Publish the config file:

```bash
php artisan vendor:publish --tag="enom-config"
```

Add your credentials to `.env`:

```env
ENOM_USERNAME=your-reseller-id
ENOM_TOKEN=your-api-token
ENOM_SANDBOX=true
```

Set `ENOM_SANDBOX=false` when you're ready to use the live environment.

## Usage

All resources are accessed through the `Enom` facade.

### Domains

A single domain is scoped through `Enom::domain($sld, $tld)`. Account-wide operations live on `Enom::domains()`.

```php
use Sensson\Enom\Facades\Enom;

Enom::domain('example', 'com')->check();
Enom::domain('example', 'com')->get();
Enom::domains()->list();
Enom::domain('example', 'com')->renew(years: 2);
```

#### Register a domain

```php
use Sensson\Enom\Data\Contact;

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

Enom::domain('example', 'com')->register($contact);
```

You can provide separate contacts for admin, tech, and billing. If omitted, the registrant contact is used for all:

```php
Enom::domain('example', 'com')->register($registrant, admin: $admin, tech: $tech, billing: $billing, years: 2);
```

#### Transfer a domain in

```php
Enom::domain('example', 'com')->transfer('authorization-code');
```

#### Lock and unlock

Lock a domain to prevent unauthorized transfers, unlock before an outgoing transfer, and read the current status:

```php
Enom::domain('example', 'com')->lock();
Enom::domain('example', 'com')->unlock();
Enom::domain('example', 'com')->getLock();
```

#### EPP / auth code (outgoing transfer)

```php
$authCode = Enom::domain('example', 'com')->getAuthCode();

echo $authCode->code;
```

#### Auto-renew

```php
Enom::domain('example', 'com')->setAutoRenew(true);

$enabled = Enom::domain('example', 'com')->getAutoRenew();
```

#### Push to another account

```php
Enom::domain('example', 'com')->push('other-reseller-account');
```

### Contacts

Get all contacts for a domain:

```php
$contacts = Enom::domain('example', 'com')->contacts()->get();

echo $contacts->registrant->first_name;
echo $contacts->admin->email;
```

Update a specific contact type:

```php
use Sensson\Enom\Enums\ContactType;

Enom::domain('example', 'com')->contacts()->update(ContactType::Admin, $contact);
```

Available contact types: `Registrant`, `Admin`, `Tech`, `Billing`.

### Nameservers

```php
use Sensson\Enom\Data\Nameservers;

$nameservers = Enom::domain('example', 'com')->nameservers()->get();

Enom::domain('example', 'com')->nameservers()->update(new Nameservers([
    'ns1.yourhost.com',
    'ns2.yourhost.com',
]));

Enom::domain('example', 'com')->nameservers()->register('ns1.example.com', '1.2.3.4');
Enom::domain('example', 'com')->nameservers()->delete('ns1.example.com');
```

`register()` creates a child nameserver (glue record); `delete()` removes one.

### DNS records

`update()` replaces all existing records for the domain:

```php
use Sensson\Enom\Data\DnsRecord;
use Sensson\Enom\Enums\DnsRecordType;

$records = Enom::domain('example', 'com')->dns()->get();

Enom::domain('example', 'com')->dns()->update([
    new DnsRecord(hostname: 'www', type: DnsRecordType::A, address: '1.2.3.4', ttl: 300),
    new DnsRecord(hostname: '@', type: DnsRecordType::MX, address: 'mail.example.com', ttl: 300, mx_preference: 10),
    new DnsRecord(hostname: '@', type: DnsRecordType::TXT, address: 'v=spf1 include:example.com ~all', ttl: 300),
]);
```

Available record types: `A`, `AAAA`, `CNAME`, `MX`, `NS`, `TXT`.

### Transfer tracking

List all transfer orders for a domain, or look up and cancel an order by its ID:

```php
$orders = Enom::domain('example', 'com')->transfers()->list();

$order = Enom::transfers()->get('12345');
Enom::transfers()->cancel('12345');
```

### Account

```php
$balance = Enom::account()->balance();

echo $balance->balance;
echo $balance->currency;
```

## Testing

Use `fake()` with Saloon's `MockClient`:

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Domains\CheckDomain;

$mock = new MockClient([
    CheckDomain::class => MockResponse::make('<interface-response><RRPCode>210</RRPCode></interface-response>'),
]);

Enom::fake($mock);

$result = Enom::domain('example', 'com')->check();

$mock->assertSent(CheckDomain::class);
```

Run the test suite:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sensson](https://github.com/sensson)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
