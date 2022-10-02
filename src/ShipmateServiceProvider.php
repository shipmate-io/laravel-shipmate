<?php

namespace Shipmate\Shipmate;

use Shipmate\Shipmate\Commands\ShipmateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShipmateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-shipmate')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-shipmate_table')
            ->hasCommand(ShipmateCommand::class);
    }
}
