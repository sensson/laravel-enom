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

### Check availability

```php
use Sensson\Enom\Facades\Enom;

$response = Enom::domains()->check('example', 'com');
```

### Register a domain

```php
use Sensson\Enom\Data\Contact;
use Sensson\Enom\Facades\Enom;

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

### Get domain info

```php
Enom::domains()->get('example', 'com');
```

### Renew a domain

```php
Enom::domains()->renew('example', 'com', years: 2);
```

### Transfer a domain

```php
Enom::domains()->transfer('example', 'com', 'authorization-code');
```

### Contacts

Get contacts for a domain:

```php
Enom::domains()->contacts('example', 'com')->get();
```

Update a specific contact type:

```php
use Sensson\Enom\Enums\ContactType;

Enom::domains()->contacts('example', 'com')->update(ContactType::Admin, $contact);
```

Available contact types: `Registrant`, `Admin`, `Tech`, `Billing`.

## Testing

Use `fake()` with Saloon's `MockClient`:

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\CheckDomain;

$mock = new MockClient([
    CheckDomain::class => MockResponse::make('<interface-response><RRPCode>210</RRPCode></interface-response>'),
]);

Enom::fake($mock);

$response = Enom::domains()->check('example', 'com');

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
