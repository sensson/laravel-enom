<?php

declare(strict_types=1);

use Sensson\Enom\Data\EnomCredentials;
use Sensson\Enom\Enom;

it('resolves the sandbox base url', function (): void {
    $connector = new Enom(new EnomCredentials('user', 'token', sandbox: true));

    expect($connector->resolveBaseUrl())->toBe('https://resellertest.enom.com');
});

it('resolves the live base url', function (): void {
    $connector = new Enom(new EnomCredentials('user', 'token', sandbox: false));

    expect($connector->resolveBaseUrl())->toBe('https://reseller.enom.com');
});
