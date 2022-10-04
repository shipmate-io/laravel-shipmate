<?php

namespace Shipmate\LaravelShipmate\StorageBucket;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class StorageBucketServiceProvider extends ServiceProvider
{
    public static function new(Application $app): static
    {
        return new static($app);
    }

    public function boot(): void
    {
        Storage::extend('shipmate', function ($app, $config) {
            $storageBucketConfig = StorageBucketConfig::new($config);

            return new StorageBucketFilesystemAdapter($storageBucketConfig);
        });
    }
}
