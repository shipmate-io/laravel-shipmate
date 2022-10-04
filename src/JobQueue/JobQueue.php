<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue as LaravelQueue;
use Shipmate\LaravelShipmate\JobQueue\Google\GoogleClient;

class JobQueue extends LaravelQueue implements QueueContract
{
    public function __construct(
        private GoogleClient $googleClient,
        private JobQueueConfig $jobQueueConfig,
    ) {
    }

    /**
     * Get the size of the queue.
     *
     * Google Cloud Tasks doesn't support retrieving the size of the queue, so we simply return 0.
     *
     * @param  string|null  $queue
     */
    public function size($queue = null): int
    {
        return 0;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string|object  $job
     * @param  string|null  $queue
     */
    public function push($job, $data = '', $queue = null): void
    {
        $queueName = $queue ?: $this->jobQueueConfig->getQueueName();

        $this->googleClient->createJob(
            queueName: $queueName,
            payload: $this->createPayload($job, $queueName, $data),
            availableAt: $this->availableAt()
        );
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     */
    public function pushRaw($payload, $queue = null, array $options = []): void
    {
        $queueName = $queue ?: $this->jobQueueConfig->getQueueName();

        $this->googleClient->createJob(
            queueName: $queueName,
            payload: $payload,
            availableAt: $this->availableAt()
        );
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  DateTimeInterface|DateInterval|int  $delay
     * @param  string|object  $job
     * @param  string|null  $queue
     */
    public function later($delay, $job, $data = '', $queue = null): void
    {
        $queueName = $queue ?: $this->jobQueueConfig->getQueueName();

        $this->googleClient->createJob(
            queueName: $queueName,
            payload: $this->createPayload($job, $queueName, $data),
            availableAt: $this->availableAt($delay)
        );
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     */
    public function pop($queue = null): ?Job
    {
        return null;
    }
}
