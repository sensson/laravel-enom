<?php

declare(strict_types=1);

namespace Sensson\Enom\Facades;

use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Sensson\Enom\Enom as EnomConnector;

/** @see EnomConnector */
class Enom extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EnomConnector::class;
    }

    public static function fake(MockClient $client): EnomConnector
    {
        $connector = new EnomConnector('fake-user', 'fake-token');

        static::swap($connector->withMockClient($client));

        return $connector;
    }
}
