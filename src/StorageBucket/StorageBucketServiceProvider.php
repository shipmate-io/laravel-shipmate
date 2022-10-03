<?php

namespace Shipmate\Shipmate\StorageBucket;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem as FlysystemDriver;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter as FlysystemAdapter;
use Shipmate\Shipmate\ShipmateConfig;

class StorageBucketServiceProvider extends ServiceProvider
{
    public static function new(Application $app): static
    {
        return new static($app);
    }

    public function boot(): void
    {
        Storage::extend('shipmate', function ($app, $config) {
            $shipmateConfig = ShipmateConfig::new();
            $storageBucketConfig = StorageBucketConfig::new($config);

            $flysystemConfig = $this->constructFlysystemConfig($storageBucketConfig);
            $client = $this->createClient($shipmateConfig);
            $adapter = $this->createAdapter($client, $storageBucketConfig);

            return new StorageBucketAdapter(
                driver: new FlysystemDriver(
                    adapter: $adapter,
                    config: $flysystemConfig
                ),
                adapter: $adapter,
                config: $flysystemConfig,
                client: $client,
            );
        });
    }

    protected function constructFlysystemConfig(StorageBucketConfig $config): array
    {
        return [
            'root' => $config->getPathPrefix(),
        ];
    }

    protected function createClient(ShipmateConfig $config): StorageClient
    {
        return new StorageClient([
            'keyFile' => $config->getKey(),
            'projectId' => $config->getProjectId(),
        ]);
    }

    protected function createAdapter(StorageClient $client, StorageBucketConfig $config): FlysystemAdapter
    {
        return new FlysystemAdapter(
            bucket: $client->bucket($config->getBucket()),
            prefix: $config->getPathPrefix(),
            visibilityHandler: null,
            defaultVisibility: $config->getVisibility(),
        );
    }
}
