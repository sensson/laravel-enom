<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Sensson\Enom\Exceptions\ApiException;
use Sensson\Enom\Exceptions\AuthenticationException;
use Sensson\Enom\Facades\Enom;
use Sensson\Enom\Requests\Domains\ListDomains;

it('throws on an in-body error response (HTTP 200 with ErrCount)', function (): void {
    $mock = new MockClient([
        ListDomains::class => MockResponse::make(
            '<interface-response><ErrCount>1</ErrCount><errors><Err1>Domain name not available for processing</Err1></errors></interface-response>'
        ),
    ]);

    Enom::fake($mock);

    Enom::domains()->list();
})->throws(ApiException::class, 'Domain name not available for processing');

it('maps invalid credentials to an authentication exception', function (): void {
    $mock = new MockClient([
        ListDomains::class => MockResponse::make(
            '<interface-response><ErrCount>1</ErrCount><errors><Err1>Authentication Error; invalid UID or Password</Err1></errors></interface-response>'
        ),
    ]);

    Enom::fake($mock);

    Enom::domains()->list();
})->throws(AuthenticationException::class);
