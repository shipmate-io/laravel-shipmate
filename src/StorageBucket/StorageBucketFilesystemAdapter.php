<?php

namespace Shipmate\LaravelShipmate\StorageBucket;

use Google\Cloud\Storage\Connection\Rest;
use Google\Cloud\Storage\StorageClient as GoogleClient;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem as FilesystemDriver;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter as GoogleFilesystemAdapter;
use Shipmate\LaravelShipmate\ShipmateConfig;

class StorageBucketFilesystemAdapter extends FilesystemAdapter
{
    protected GoogleClient $googleClient;

    public function __construct(
        protected StorageBucketConfig $storageBucketConfig,
    ) {
        $shipmateConfig = ShipmateConfig::new();

        $this->googleClient = new GoogleClient([
            'keyFile' => $shipmateConfig->getKey(),
            'projectId' => $shipmateConfig->getProjectId(),
        ]);

        $adapter = new GoogleFilesystemAdapter(
            bucket: $this->googleClient->bucket($storageBucketConfig->getBucketName()),
            prefix: $storageBucketConfig->getPathPrefix(),
            visibilityHandler: null,
            defaultVisibility: $storageBucketConfig->getVisibility(),
        );

        $filesystemConfig = [
            'root' => $storageBucketConfig->getPathPrefix(),
        ];

        $driver = new FilesystemDriver(
            adapter: $adapter,
            config: $filesystemConfig
        );

        parent::__construct($driver, $adapter, $filesystemConfig);
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \RuntimeException
     */
    public function url($path): string
    {
        $bucketName = $this->storageBucketConfig->getBucketName();

        $storageApiUri = rtrim(Rest::DEFAULT_API_ENDPOINT, '/').'/'.ltrim($bucketName, '/');

        $fullPath = $this->prefixer->prefixPath($path);

        return $this->concatPathToUrl($storageApiUri, $fullPath);
    }

    /**
     * Get a temporary URL for the file at the given path.
     *
     * @param  string  $path
     * @param  \DateTimeInterface  $expiration
     * @param  array  $options
     * @return string
     */
    public function temporaryUrl($path, $expiration, array $options = []): string
    {
        $bucketName = $this->storageBucketConfig->getBucketName();

        $fullPath = $this->prefixer->prefixPath($path);

        return $this->googleClient->bucket($bucketName)->object($fullPath)->signedUrl($expiration, $options);
    }
}
