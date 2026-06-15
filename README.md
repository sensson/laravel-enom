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

All resources are accessed through the `Enom` facade. A domain is addressed by its second-level and
top-level label, e.g. `Enom::domains()->get('example', 'com')` for `example.com`.

### Domains

```php
use Sensson\Enom\Facades\Enom;

$domain = Enom::domains()->get('example', 'com');   // status, expires_at, dnssec
$domains = Enom::domains()->list();
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

You can provide separate contacts for admin, tech, and billing. If omitted, the registrant contact is used
for all:

```php
Enom::domains()->register('example', 'com', $registrant, admin: $admin, tech: $tech, billing: $billing, years: 2);
```

#### Transfer a domain in

```php
Enom::domains()->transfer('example', 'com', 'authorization-code');
```

#### Lock and unlock

Lock a domain to prevent unauthorized transfers, unlock before an outgoing transfer, and read the current
status:

```php
Enom::domains()->lock('example', 'com');
Enom::domains()->unlock('example', 'com');

$locked = Enom::domains()->locked('example', 'com');
```

#### EPP / auth code (outgoing transfer)

```php
$authCode = Enom::domains()->getAuthCode('example', 'com');

echo $authCode->code;
```

#### Push to another account

```php
Enom::domains()->push('example', 'com', 'other-reseller-account');
```

### Contacts

```php
use Sensson\Enom\Enums\Type;

$contacts = Enom::domains()->contacts('example', 'com')->get();

echo $contacts->registrant->first_name;
echo $contacts->admin->email;

Enom::domains()->contacts('example', 'com')->update(Type::Admin, $contact);
```

Available contact types: `Type::Registrant`, `Type::Admin`, `Type::Tech`, `Type::Billing`.

### Nameservers

```php
use Sensson\Enom\Data\Nameservers;

$nameservers = Enom::domains()->nameservers('example', 'com')->get();

Enom::domains()->nameservers('example', 'com')->update(new Nameservers([
    'ns1.yourhost.com',
    'ns2.yourhost.com',
]));

Enom::domains()->nameservers('example', 'com')->register('ns1.example.com', '1.2.3.4');
Enom::domains()->nameservers('example', 'com')->delete('ns1.example.com');
```

`register()` creates a child nameserver (glue record); `delete()` removes one.

### DNSSEC

Sign a domain with a DS record, or remove one. The current records are returned on the domain by `get()`:

```php
use Sensson\Enom\Data\Dnssec;

$records = Enom::domains()->get('example', 'com')->dnssec;

Enom::domains()->sign('example', 'com', new Dnssec(
    key_tag: 12345,
    algorithm: 8,
    digest_type: 2,
    digest: 'ABCDEF1234567890',
));

Enom::domains()->unsign('example', 'com', $records[0]);
```

Removing a record requires all four values to match what is set at the registry, so pass back a record from
`get()`.

### Transfer tracking

List all transfer orders for a domain, or look up and cancel an order by its id:

```php
$orders = Enom::domains()->transfers('example', 'com')->list();

$order = Enom::domains()->transfers('example', 'com')->get('12345');
Enom::domains()->transfers('example', 'com')->cancel('12345');
```

### Account

```php
$balance = Enom::account()->balance();

echo $balance->amount;
echo $balance->currency;
```

### Multiple connections

Define extra connections in `config/enom.php` and select one by name, or supply credentials at runtime:

```php
use Sensson\Enom\Data\Connection;

Enom::connection('reseller2')->domains()->list();

Enom::build(new Connection('username', 'token', sandbox: false))->domains()->list();
```

## Testing

Use `fake()` with Saloon's `MockClient`:

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Domains\ListDomains;

$mock = new MockClient([
    ListDomains::class => MockResponse::make('<interface-response><GetAllDomains></GetAllDomains></interface-response>'),
]);

Enom::fake($mock);

Enom::domains()->list();

$mock->assertSent(ListDomains::class);
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
