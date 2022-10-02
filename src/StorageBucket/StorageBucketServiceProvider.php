<?php

namespace Shipmate\Shipmate\StorageBucket;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem as FlysystemDriver;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter as FlysystemAdapter;

class StorageBucketServiceProvider
{
    public static function new(): static
    {
        return new static;
    }

    public function boot(): void
    {
        Storage::extend('shipmate', function ($app, $originalConfig) {
            $config = StorageBucketConfig::new($originalConfig);
            $flysystemConfig = $this->constructFlysystemConfig($config);
            $client = $this->createClient($config);
            $adapter = $this->createAdapter($client, $config);

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

    protected function createClient(StorageBucketConfig $config): StorageClient
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
