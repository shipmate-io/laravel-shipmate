<?php

namespace Shipmate\Shipmate;

use Illuminate\Container\Container;
use Shipmate\Shipmate\JobQueue\JobQueueServiceProvider;
use Shipmate\Shipmate\MessageQueue\MessageQueueServiceProvider;
use Shipmate\Shipmate\StorageBucket\StorageBucketServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShipmateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-shipmate')
            ->hasConfigFile('shipmate');

        MessageQueueServiceProvider::new($this->app)->configurePackage($package);
    }

    public function packageBooted(): void
    {
        $this->app->singleton(
            abstract: ShipmateConfig::class,
            concrete: fn (Container $app) => new ShipmateConfig($app['config']->get('shipmate'))
        );

        JobQueueServiceProvider::new($this->app)->boot();
        MessageQueueServiceProvider::new($this->app)->boot();
        StorageBucketServiceProvider::new($this->app)->boot();
    }
}
