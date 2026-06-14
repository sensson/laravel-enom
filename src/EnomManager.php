<?php

declare(strict_types=1);

namespace Sensson\Enom;

use InvalidArgumentException;
use Sensson\Enom\Data\Connection;

/**
 * @mixin Enom
 */
class EnomManager
{
    /** @var array<string, Enom> */
    protected array $connections = [];

    public function connection(?string $name = null): Enom
    {
        $name ??= $this->getDefaultConnection();

        return $this->connections[$name] ??= $this->resolve($name);
    }

    public function build(Connection $connection): Enom
    {
        return $this->make($connection);
    }

    protected function resolve(string $name): Enom
    {
        return $this->make($this->getConfig($name));
    }

    protected function make(Connection $connection): Enom
    {
        return new Enom(
            username: $connection->username,
            token: $connection->token,
            sandbox: $connection->sandbox,
        );
    }

    protected function getConfig(string $name): Connection
    {
        $config = config("enom.connections.{$name}");

        if (! is_array($config)) {
            throw new InvalidArgumentException("Enom connection [{$name}] is not configured.");
        }

        return Connection::from($config);
    }

    protected function getDefaultConnection(): string
    {
        return config()->string('enom.default');
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->connection()->{$method}(...$parameters);
    }
}
