<?php

declare(strict_types=1);

use Sensson\Enom\Data\Contact;
use Sensson\Enom\Enums\ContactType;
use Sensson\Enom\Facades\Enom;

it('checks domain availability', function (): void {
    $response = Enom::domains()->check('sensson-test-domain', 'com');

    expect($response->status())->toBe(200);
});

it('registers a domain', function (): void {
    $contact = new Contact(
        first_name: 'John',
        last_name: 'Doe',
        organization: 'Sensson',
        address: '123 Main St',
        city: 'Springfield',
        state: 'IL',
        postal_code: '62701',
        country: 'US',
        phone: '+1.5551234567',
        email: 'john@example.com',
    );

    $response = Enom::domains()->register('sensson-test-domain', 'com', $contact);

    expect($response->status())->toBe(200);
})->depends('it checks domain availability');

it('gets domain info', function (): void {
    $response = Enom::domains()->get('sensson-test-domain', 'com');

    expect($response->status())->toBe(200);
})->depends('it registers a domain');

it('gets contacts', function (): void {
    $response = Enom::domains()->contacts('sensson-test-domain', 'com')->get();

    expect($response->status())->toBe(200);
})->depends('it registers a domain');

it('updates a contact', function (): void {
    $contact = new Contact(
        first_name: 'Jane',
        last_name: 'Doe',
        organization: 'Sensson',
        address: '456 Oak Ave',
        city: 'Springfield',
        state: 'IL',
        postal_code: '62701',
        country: 'US',
        phone: '+1.5559876543',
        email: 'jane@example.com',
    );

    $response = Enom::domains()->contacts('sensson-test-domain', 'com')->update(ContactType::Admin, $contact);

    expect($response->status())->toBe(200);
})->depends('it gets contacts');

it('renews a domain', function (): void {
    $response = Enom::domains()->renew('sensson-test-domain', 'com');

    expect($response->status())->toBe(200);
})->depends('it registers a domain');
