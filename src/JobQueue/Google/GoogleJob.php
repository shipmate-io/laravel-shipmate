<?php

namespace Shipmate\Shipmate\JobQueue\Google;

use Google\Cloud\Tasks\V2\Attempt;
use Google\Cloud\Tasks\V2\Task;
use Google\Protobuf\Duration;
use Google\Protobuf\Timestamp;

class GoogleJob
{
    public function __construct(
        private GoogleClient $api,
        private string $queueName,
        private Task $task,
    ) {
    }

    public function getRetryUntilTimestamp(): ?int
    {
        $attempt = $this->task->getFirstAttempt();

        if (! $attempt instanceof Attempt) {
            return null;
        }

        $retryConfig = $this->api->getQueue($this->queueName)->getRetryConfig();

        $maxRetryDuration = $retryConfig->getMaxRetryDuration();
        $dispatchTime = $attempt->getDispatchTime();

        if (! $maxRetryDuration instanceof Duration || ! $dispatchTime instanceof Timestamp) {
            return null;
        }

        $maxDurationInSeconds = (int) $maxRetryDuration->getSeconds();

        $firstAttemptTimestamp = $dispatchTime->toDateTime()->getTimestamp();

        return $firstAttemptTimestamp + $maxDurationInSeconds;
    }
}
