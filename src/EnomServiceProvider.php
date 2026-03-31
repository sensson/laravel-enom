<?php

namespace Sensson\Enom;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sensson\Enom\Commands\EnomCommand;

class EnomServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-enom')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_enom_table')
            ->hasCommand(EnomCommand::class);
    }
}
