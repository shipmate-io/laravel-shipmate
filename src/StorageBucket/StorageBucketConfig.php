<?php

namespace Shipmate\Shipmate\StorageBucket;

use Illuminate\Support\Arr;
use League\Flysystem\Visibility;
use Shipmate\Shipmate\ShipmateException;

class StorageBucketConfig
{
    public function __construct(
        private array $config
    ) {
    }

    public static function new(array $config): static
    {
        return new static($config);
    }

    public function getBucketName(): string
    {
        $bucketName = $this->config['bucket'] ?? null;

        if (! $bucketName) {
            throw new ShipmateException(
                'No value specified for the `bucket` parameter of the `shipmate` filesystem in the `config/filesystems.php` file.'
            );
        }

        return $bucketName;
    }

    public function getPathPrefix(): string
    {
        return $this->config['path_prefix'] ?? '';
    }

    public function getVisibility(): string
    {
        $visibility = $this->config['visibility'] ?? null;

        $validOptions = [
            Visibility::PRIVATE,
            Visibility::PUBLIC,
        ];

        return in_array($visibility, $validOptions) ? $visibility : Visibility::PRIVATE;
    }
}
