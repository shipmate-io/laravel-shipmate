<?php

namespace Shipmate\LaravelShipmate\StorageBucket;

use Google\Cloud\Storage\Connection\Rest;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem as FilesystemDriver;
use Shipmate\Shipmate\StorageBucket\StorageBucketFilesystemAdapter as PhpAdapter;

class StorageBucketFilesystemAdapter extends FilesystemAdapter
{
    private PhpAdapter $phpAdapter;

    public function __construct(
        protected StorageBucketConfig $storageBucketConfig,
    ) {
        $this->phpAdapter = new PhpAdapter(
            bucketName: $storageBucketConfig->getBucketName(),
            visibility: $storageBucketConfig->getVisibility(),
        );

        $driver = new FilesystemDriver(
            adapter: $this->phpAdapter,
            config: []
        );

        parent::__construct($driver, $this->phpAdapter);
    }

    /**
     * Get the URL for the file at the given path.
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
     */
    public function temporaryUrl($path, $expiration, array $options = []): string
    {
        $bucketName = $this->storageBucketConfig->getBucketName();

        $fullPath = $this->prefixer->prefixPath($path);

        return $this->phpAdapter
            ->getGoogleClient()
            ->bucket($bucketName)
            ->object($fullPath)
            ->signedUrl($expiration, $options);
    }
}
