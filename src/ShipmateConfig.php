<?php

namespace Shipmate\LaravelShipmate;

class ShipmateConfig
{
    public function __construct(
        private array $config
    ) {
    }

    public static function new(): static
    {
        return app(static::class);
    }

    /**
     * The service account email used to authenticate with Shipmate.
     */
    public function getEmail(): string
    {
        $email = $this->config['email'] ?? env('SHIPMATE_EMAIL');

        if (! $email) {
            throw new ShipmateException(
                'No `email` specified in the Shipmate configuration in the `config/services.php` file.'
            );
        }

        return $email;
    }

    /**
     * The service account key used to authenticate with Shipmate.
     */
    public function getKey(): array
    {
        $key = $this->config['key'] ?? env('SHIPMATE_KEY');

        if (! $key) {
            throw new ShipmateException(
                'No `key` specified in the Shipmate configuration in the `config/services.php` file.'
            );
        }

        $decodedKey = json_decode(base64_decode($key), true);

        if (! is_array($decodedKey)) {
            throw new ShipmateException(
                'The `key` specified in the Shipmate configuration in the `config/services.php` file is not a valid key.'
            );
        }

        return $decodedKey;
    }

    /**
     * The id of the Google Cloud project under which the queue is created.
     */
    public function getProjectId(): string
    {
        $projectId = $this->config['project_id'] ?? env('SHIPMATE_PROJECT_ID');

        if (! $projectId) {
            throw new ShipmateException(
                'No `project_id` specified in the Shipmate configuration in the `config/services.php` file.'
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
                'No `region_id` specified in the Shipmate configuration in the `config/services.php` file.'
            );
        }

        return $regionId;
    }
}
