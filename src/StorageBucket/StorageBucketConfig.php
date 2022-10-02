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

    public function getKey(): string
    {
        $key = Arr::get($this->config, 'key', env('SHIPMATE_KEY'));

        if (! $key) {
            throw new ShipmateException('No value specified for the `key` parameter in the Shipmate storage bucket configuration.');
        }

        return json_decode(base64_decode($key), true);
    }

    public function getProjectId(): string
    {
        $projectId = Arr::get($this->config, 'project_id', env('SHIPMATE_PROJECT_ID'));

        if (! $projectId) {
            throw new ShipmateException('No value specified for the `project_id` parameter in the Shipmate storage bucket configuration.');
        }

        return $projectId;
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
