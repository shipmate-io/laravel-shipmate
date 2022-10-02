<?php

namespace Shipmate\Shipmate\JobQueue;

use Shipmate\Shipmate\ShipmateException;
use Spatie\Url\Url;

class JobQueueConfig
{
    public function __construct(
        private array $config
    ) {
    }

    public static function readFromConnection(string $connectionName): static
    {
        return app(static::class, [
            'config' => config("queue.connections.{$connectionName}"),
        ]);
    }

    /**
     * The id of the Google Cloud project under which the queue is created.
     */
    public function getProjectId(): string
    {
        $projectId = $this->config['project_id'] ?? null;

        if (! $projectId) {
            throw new ShipmateException(
                'No value specified for the `project_id` parameter in the `config/queue.php` file.'
            );
        }

        return $projectId;
    }

    /**
     * The name of the Google Cloud region where the queue is created.
     */
    public function getRegionId(): string
    {
        $regionId = $this->config['region_id'] ?? null;

        if (! $regionId) {
            throw new ShipmateException(
                'No value specified for the `region_id` parameter in the `config/queue.php` file.'
            );
        }

        return $regionId;
    }

    /**
     * The name of the queue.
     */
    public function getQueueName(): string
    {
        $queueName = $this->config['queue'] ?? null;

        if (! $queueName) {
            throw new ShipmateException(
                'No value specified for the `queue` parameter in the `config/queue.php` file.'
            );
        }

        return $queueName;
    }

    /**
     * The service account email used to authenticate with Google Cloud.
     */
    public function getEmail(): string
    {
        $email = $this->config['email'] ?? null;

        if (! $email) {
            throw new ShipmateException(
                'No value specified for the `email` parameter in the `config/queue.php` file.'
            );
        }

        return $email;
    }

    /**
     * The contents of the service account key file used to authenticate with Google Cloud.
     */
    public function getKey(): array
    {
        if (! $this->config['key'] ?? null) {
            return [];
        }

        return json_decode(base64_decode($this->config['key']), true);
    }

    /**
     * The URL of the background worker that is consuming the queue. If no value is specified, the URL of the current
     * service is used.
     */
    public function getWorkerUrl(): string
    {
        $url = $this->config['worker_url'] ?? request()->getSchemeAndHttpHost();

        return Url::fromString($url)->withPath('handle-job');
    }
}
