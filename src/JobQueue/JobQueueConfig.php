<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Shipmate\LaravelShipmate\ShipmateException;
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
     * The URL of the background worker that is consuming the queue. If no value is specified, the URL of the current
     * service is used.
     */
    public function getWorkerUrl(): string
    {
        $url = Url::fromString($this->config['worker_url'] ?? request()->getSchemeAndHttpHost());

        if ($url->getScheme() === '') {
            throw new ShipmateException(
                'The value specified for the `worker_url` parameter in the `config/queue.php` file is not a valid URL.'
            );
        }

        return $url->withPath('shipmate/handle-job');
    }
}
