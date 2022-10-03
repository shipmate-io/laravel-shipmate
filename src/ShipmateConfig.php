<?php

namespace Shipmate\Shipmate;

class ShipmateConfig
{
    public function __construct(
        private array $config
    ) {
    }

    public static function new(array $config): static
    {
        return new static($config);
    }

    /**
     * The service account email used to authenticate with Shipmate.
     */
    public function getEmail(): string
    {
        $email = $this->config['email'] ?? env('SHIPMATE_EMAIL');

        if (! $email) {
            throw new ShipmateException(
                'No value specified for the `email` parameter in the `config/shipmate.php` file.'
            );
        }

        return $email;
    }

    /**
     * The service account key used to authenticate with Shipmate.
     */
    public function getKey(): string
    {
        $key = $this->config['key'] ?? env('SHIPMATE_KEY');

        if (! $key) {
            throw new ShipmateException(
                'No value specified for the `key` parameter in the `config/shipmate.php` file.'
            );
        }

        return json_decode(base64_decode($key), true);
    }

    /**
     * The id of the Google Cloud project under which the queue is created.
     */
    public function getProjectId(): string
    {
        $projectId = $this->config['project_id'] ?? env('SHIPMATE_PROJECT_ID');

        if (! $projectId) {
            throw new ShipmateException(
                'No value specified for the `project_id` parameter in the `config/shipmate.php` file.'
            );
        }

        return $projectId;
    }

    /**
     * The name of the Google Cloud region where the queue is created.
     */
    public function getRegionId(): string
    {
        $regionId = $this->config['region_id'] ?? env('SHIPMATE_REGION_ID');

        if (! $regionId) {
            throw new ShipmateException(
                'No value specified for the `region_id` parameter in the `config/shipmate.php` file.'
            );
        }

        return $regionId;
    }
}
