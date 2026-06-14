<?php

declare(strict_types=1);

namespace Sensson\Enom\Exceptions;

use RuntimeException;

abstract class EnomException extends RuntimeException
{
    public static function fromMessage(string $message): self
    {
        $error = str($message)->lower();

        $exception = match (true) {
            $error->contains(['authentication', 'authorization', 'invalid login', 'invalid account', 'invalid uid', 'invalid password']) => AuthenticationException::class,
            $error->contains(['not found', 'does not exist', 'no such domain']) => NotFoundException::class,
            $error->contains(['too many', 'rate limit', 'throttle', 'try again later']) => RateLimitException::class,
            $error->contains(['required', 'missing', 'invalid', 'not valid', 'malformed']) => ValidationException::class,
            default => ApiException::class,
        };

        return new $exception($message);
    }
}
