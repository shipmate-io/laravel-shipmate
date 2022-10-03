<?php

namespace Shipmate\Shipmate\JobQueue;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Shipmate\Shipmate\JobQueue\Google\GoogleClient;
use Shipmate\Shipmate\ShipmateConfig;

class JobQueueConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     */
    public function connect(array $config): Queue
    {
        $shipmateConfig = ShipmateConfig::new();
        $jobQueueConfig = new JobQueueConfig($config);

        return new JobQueue(
            googleClient: new GoogleClient(
                shipmateConfig: $shipmateConfig,
                jobQueueConfig: $jobQueueConfig,
            ),
            jobQueueConfig: $jobQueueConfig,
        );
    }
}
