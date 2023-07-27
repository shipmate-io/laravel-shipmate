<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Google\Cloud\Tasks\V2\Attempt;
use Google\Cloud\Tasks\V2\RetryConfig;
use Google\Protobuf\Duration;
use Google\Protobuf\Timestamp;
use Shipmate\Shipmate\JobQueue\JobQueue as PhpJobQueue;
use Shipmate\Shipmate\ShipmateException;

class JobQueue extends PhpJobQueue
{
    public static function parseJob(string $requestPayload): Job
    {
        $phpJob = parent::parseJob($requestPayload);

        return new Job(
            payload: $phpJob->payload
        );
    }

    // Queues

    public function getMaxTries(): int
    {
        $fullyQualifiedQueueName = $this->generateQueueName();

        $queue = $this->googleClient->getQueue($fullyQualifiedQueueName);

        $retryConfig = $queue->getRetryConfig();

        if (! $retryConfig instanceof RetryConfig) {
            throw new ShipmateException('Queue does not have a retry config.');
        }

        $maxTries = $retryConfig->getMaxAttempts();

        return $maxTries === -1 ? 0 : $maxTries;
    }

    // Jobs

    public function getRetryDeadlineOfJob(string $jobName): ?int
    {
        $fullyQualifiedTaskName = $this->generateTaskName($jobName);

        $task = $this->googleClient->getTask($fullyQualifiedTaskName);

        $attempt = $task->getFirstAttempt();

        if (! $attempt instanceof Attempt) {
            return null;
        }

        $fullyQualifiedQueueName = $this->generateQueueName();

        $queue = $this->googleClient->getQueue($fullyQualifiedQueueName);

        $retryConfig = $queue->getRetryConfig();

        if (! $retryConfig instanceof RetryConfig) {
            throw new ShipmateException('Queue does not have a retry config.');
        }

        $maxRetryDuration = $retryConfig->getMaxRetryDuration();
        $dispatchTime = $attempt->getDispatchTime();

        if (! $maxRetryDuration instanceof Duration || ! $dispatchTime instanceof Timestamp) {
            return null;
        }

        $maxDurationInSeconds = (int) $maxRetryDuration->getSeconds();

        $firstAttemptTimestamp = $dispatchTime->toDateTime()->getTimestamp();

        return $firstAttemptTimestamp + $maxDurationInSeconds;
    }

    public function deleteJob(string $jobName): void
    {
        $fullyQualifiedTaskName = $this->generateTaskName($jobName);

        $this->googleClient->deleteTask($fullyQualifiedTaskName);
    }

    // Helpers

    private function generateQueueName(): string
    {
        return $this->googleClient->queueName(
            project: $this->shipmateConfig->getEnvironmentId(),
            location: $this->shipmateConfig->getRegionId(),
            queue: $this->name,
        );
    }

    private function generateTaskName(string $taskName): string
    {
        return $this->googleClient->taskName(
            project: $this->shipmateConfig->getEnvironmentId(),
            location: $this->shipmateConfig->getRegionId(),
            queue: $this->name,
            task: $taskName
        );
    }
}
