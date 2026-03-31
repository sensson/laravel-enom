<?php

namespace Sensson\Enom;

use Sensson\Enom\Commands\EnomCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
