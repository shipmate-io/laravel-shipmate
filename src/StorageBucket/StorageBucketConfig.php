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

    public function getBucket(): string
    {
        $bucket = Arr::get($this->config, 'bucket');

        if (! $bucket) {
            throw new ShipmateException('No value specified for the `bucket` parameter in the Shipmate storage bucket configuration.');
        }

        return $bucket;
    }

    public function getPathPrefix(): string
    {
        return Arr::get($this->config, 'path_prefix', '');
    }

    public function getVisibility(): string
    {
        $visibility = Arr::get($this->config, 'visibility');

        $validOptions = [
            Visibility::PRIVATE,
            Visibility::PUBLIC,
        ];

        return in_array($visibility, $validOptions) ? $visibility : Visibility::PRIVATE;
    }
}
