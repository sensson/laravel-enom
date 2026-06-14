<?php

declare(strict_types=1);

namespace Sensson\Enom;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EnomServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-enom')
            ->hasConfigFile('enom');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(EnomManager::class);
    }
}
