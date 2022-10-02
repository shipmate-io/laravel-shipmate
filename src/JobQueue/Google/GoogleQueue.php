<?php

namespace Shipmate\Shipmate\JobQueue\Google;

use Google\Cloud\Tasks\V2\Queue;
use Google\Cloud\Tasks\V2\RetryConfig;
use Shipmate\Shipmate\ShipmateException;

class GoogleQueue
{
    public function __construct(
        private Queue $queue
    ) {
    }

    public function getRetryConfig(): RetryConfig
    {
        $retryConfig = $this->queue->getRetryConfig();

        if (! $retryConfig instanceof RetryConfig) {
            throw new ShipmateException('Queue does not have a retry config.');
        }

        return $retryConfig;
    }
}
