<?php

declare(strict_types=1);

use Sensson\Enom\Enom;

it('resolves the sandbox base url', function (): void {
    $connector = new Enom('user', 'token', sandbox: true);

    expect($connector->resolveBaseUrl())->toBe('https://resellertest.enom.com');
});

it('resolves the live base url', function (): void {
    $connector = new Enom('user', 'token', sandbox: false);

    expect($connector->resolveBaseUrl())->toBe('https://reseller.enom.com');
});
