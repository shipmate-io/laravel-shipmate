<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Illuminate\Contracts\Queue\Queue as IlluminateQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class JobQueueConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     */
    public function connect(array $config): IlluminateQueue
    {
        return new Queue(
            jobQueueConfig: new JobQueueConfig($config),
        );
    }
}
