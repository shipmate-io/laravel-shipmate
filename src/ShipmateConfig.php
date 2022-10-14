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
     * The access ID used to authenticate with Shipmate.
     */
    public function getAccessId(): string
    {
        $accessId = $this->config['access_id'] ?? env('SHIPMATE_ACCESS_ID');

        if (! $accessId) {
            throw new ShipmateException(
                'No `access_id` specified in the Shipmate configuration in the `config/services.php` file.'
            );
        }

        return $accessId;
    }

    /**
     * The access key used to authenticate with Shipmate.
     */
    public function getAccessKey(): array
    {
        $accessKey = $this->config['access_key'] ?? env('SHIPMATE_ACCESS_KEY');

        if (! $accessKey) {
            throw new ShipmateException(
                'No `access_key` specified in the Shipmate configuration in the `config/services.php` file.'
            );
        }

        $decodedAccessKey = json_decode(base64_decode($accessKey), true);

        if (! is_array($decodedAccessKey)) {
            throw new ShipmateException(
                'The `access_key` specified in the Shipmate configuration in the `config/services.php` file is not a valid key.'
            );
        }

        return $decodedAccessKey;
    }

    /**
     * The id of the Shipmate environment.
     */
    public function getEnvironmentId(): string
    {
        $environmentId = $this->config['environment_id'] ?? env('SHIPMATE_ENVIRONMENT_ID');

        if (! $environmentId) {
            throw new ShipmateException(
                'No `environment_id` specified in the Shipmate configuration in the `config/services.php` file.'
            );
        }

        return $environmentId;
    }

    /**
     * The id of the region in which the Shipmate environment is created.
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
