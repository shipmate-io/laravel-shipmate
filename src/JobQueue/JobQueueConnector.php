<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Shipmate\LaravelShipmate\JobQueue\Google\GoogleClient;

class JobQueueConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     */
    public function connect(array $config): Queue
    {
        $jobQueueConfig = new JobQueueConfig($config);

        return new JobQueue(
            googleClient: new GoogleClient(
                jobQueueConfig: $jobQueueConfig,
            ),
            jobQueueConfig: $jobQueueConfig,
        );
    }
}
