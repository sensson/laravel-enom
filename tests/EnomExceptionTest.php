<?php

declare(strict_types=1);

use Sensson\Enom\Exceptions\ApiException;
use Sensson\Enom\Exceptions\AuthenticationException;
use Sensson\Enom\Exceptions\EnomException;
use Sensson\Enom\Exceptions\NotFoundException;
use Sensson\Enom\Exceptions\RateLimitException;
use Sensson\Enom\Exceptions\ValidationException;

it('maps error messages to the right exception', function (string $message, string $expected): void {
    expect(EnomException::fromMessage($message))->toBeInstanceOf($expected);
})->with([
    'authentication' => ['Authentication Error; invalid UID or Password', AuthenticationException::class],
    'not found' => ['Domain does not exist', NotFoundException::class],
    'rate limit' => ['Too many requests', RateLimitException::class],
    'validation' => ['Required parameter missing', ValidationException::class],
    'generic' => ['Something went wrong', ApiException::class],
]);
