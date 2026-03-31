<?php

namespace Sensson\Enom\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sensson\Enom\EnomServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            EnomServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app->useEnvironmentPath(__DIR__.'/..');
    }
}
