<?php

declare(strict_types=1);

namespace Sensson\Enom\Facades;

use Illuminate\Support\Facades\Facade;
use Saloon\Http\Faking\MockClient;
use Sensson\Enom\Data\Connection;
use Sensson\Enom\Enom as EnomConnector;
use Sensson\Enom\EnomManager;
use Sensson\Enom\Resources\AccountResource;
use Sensson\Enom\Resources\DomainsResource;

/**
 * @see EnomManager
 *
 * @method static EnomConnector connection(?string $name = null)
 * @method static EnomConnector build(Connection $connection)
 * @method static DomainsResource domains()
 * @method static AccountResource account()
 * @method static void test()
 */
class Enom extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EnomManager::class;
    }

    public static function fake(MockClient $client): EnomConnector
    {
        $connector = new EnomConnector('fake-user', 'fake-token');

        static::swap($connector->withMockClient($client));

        return $connector;
    }
}
