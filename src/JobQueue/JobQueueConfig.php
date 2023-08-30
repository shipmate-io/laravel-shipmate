<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Shipmate\Shipmate\ShipmateException;

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
     * Get the alias of the default queue.
     */
    public function getDefaultQueue(): string
    {
        $defaultQueue = $this->config['default_queue'] ?? null;

        if (! $defaultQueue) {
            throw new ShipmateException(
                'No `default_queue` specified on the Shipmate connection in the `config/queue.php` file.'
            );
        }

        return $defaultQueue;
    }

    /**
     * Get the name of the queue.
     */
    public function getJobQueueName(string $queue): string
    {
        $jobQueueName = $this->config['queues'][$queue]['name'] ?? null;

        if (! $jobQueueName) {
            throw new ShipmateException(
                "No name found for queue `{$queue}` on the Shipmate connection in the `config/queue.php` file."
            );
        }

        return $jobQueueName;
    }

    /**
     * Get the url of the background worker that is consuming the queue.
     */
    public function getJobQueueWorkerUrl(string $queue): string
    {
        $jobQueueWorkerUrl = $this->config['queues'][$queue]['worker_url'] ?? null;

        if (! $jobQueueWorkerUrl) {
            throw new ShipmateException(
                "No worker url found for queue `{$queue}` on the Shipmate connection in the `config/queue.php` file."
            );
        }

        return $jobQueueWorkerUrl;
    }
}
