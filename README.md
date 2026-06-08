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

```php
use Sensson\Enom\Facades\Enom;

// Check availability
Enom::domains()->check('example', 'com');

// Get domain info
Enom::domains()->get('example', 'com');

// List all domains in the account
Enom::domains()->list();

// Renew a domain
Enom::domains()->renew('example', 'com', years: 2);
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

Enom::domains()->register('example', 'com', $contact);
```

You can provide separate contacts for admin, tech, and billing. If omitted, the registrant contact is used for all:

```php
Enom::domains()->register('example', 'com', $registrant, admin: $admin, tech: $tech, billing: $billing, years: 2);
```

#### Transfer a domain in

```php
Enom::domains()->transfer('example', 'com', 'authorization-code');
```

#### Lock and unlock

```php
// Lock a domain to prevent unauthorized transfers
Enom::domains()->lock('example', 'com');

// Unlock before initiating an outgoing transfer
Enom::domains()->unlock('example', 'com');

// Get current lock status
Enom::domains()->getLock('example', 'com');
```

#### EPP / auth code (outgoing transfer)

```php
$authCode = Enom::domains()->getAuthCode('example', 'com');

echo $authCode->code; // EPP-SECRET-123
```

#### Auto-renew

```php
Enom::domains()->setAutoRenew('example', 'com', true);

$enabled = Enom::domains()->getAutoRenew('example', 'com');
```

#### Push to another account

```php
Enom::domains()->push('example', 'com', 'other-reseller-account');
```

### Contacts

Get all contacts for a domain:

```php
$contacts = Enom::domains()->contacts('example', 'com')->get();

echo $contacts->registrant->first_name;
echo $contacts->admin->email;
```

Update a specific contact type:

```php
use Sensson\Enom\Enums\ContactType;

Enom::domains()->contacts('example', 'com')->update(ContactType::Admin, $contact);
```

Available contact types: `Registrant`, `Admin`, `Tech`, `Billing`.

### Nameservers

```php
// Get current nameservers
$nameservers = Enom::domains()->nameservers('example', 'com')->get();

// Update nameservers
Enom::domains()->nameservers('example', 'com')->update([
    'ns1.yourhost.com',
    'ns2.yourhost.com',
]);

// Register a child nameserver (glue record)
Enom::domains()->nameservers('example', 'com')->register('ns1.example.com', '1.2.3.4');

// Delete a child nameserver
Enom::domains()->nameservers('example', 'com')->delete('ns1.example.com');
```

### DNS records

```php
use Sensson\Enom\Data\DnsRecord;
use Sensson\Enom\Enums\DnsRecordType;

// Get all DNS records
$records = Enom::domains()->dns('example', 'com')->get();

// Update DNS records (replaces all existing records)
Enom::domains()->dns('example', 'com')->update([
    new DnsRecord(hostname: 'www', type: DnsRecordType::A, address: '1.2.3.4', ttl: 300),
    new DnsRecord(hostname: '@', type: DnsRecordType::MX, address: 'mail.example.com', ttl: 300, mx_preference: 10),
    new DnsRecord(hostname: '@', type: DnsRecordType::TXT, address: 'v=spf1 include:example.com ~all', ttl: 300),
]);
```

Available record types: `A`, `AAAA`, `CNAME`, `MX`, `NS`, `TXT`.

### Transfer tracking

```php
// Get a transfer order by ID
$order = Enom::transfers()->get('12345');

// Get all transfer orders for a domain
$orders = Enom::transfers()->getByDomain('example', 'com');

// Cancel a transfer
Enom::transfers()->cancel('12345');
```

### Account

```php
$balance = Enom::account()->balance();

echo $balance->balance;   // 250.0
echo $balance->currency;  // USD
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

$result = Enom::domains()->check('example', 'com');

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
