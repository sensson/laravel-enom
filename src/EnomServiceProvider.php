<?php

declare(strict_types=1);

namespace Sensson\Enom;

use Sensson\Enom\Commands\EnomCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EnomServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-enom')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Enom::class, fn (): Enom => new Enom(
            username: config()->string('enom.username'),
            token: config()->string('enom.token'),
            sandbox: config()->boolean('enom.sandbox', true),
        ));
    }
}
