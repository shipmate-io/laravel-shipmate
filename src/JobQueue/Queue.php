<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\Queue\Job as IlluminateJob;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue as IlluminateQueue;
use Shipmate\Shipmate\JobQueue\Job;

class Queue extends IlluminateQueue implements QueueContract
{
    public function __construct(
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
        $queue ??= $this->jobQueueConfig->getDefaultQueue();

        $this->instantiateJobQueue($queue)->publishJob(
            job: new Job(
                payload: $this->createPayload($job, $queue, $data),
            ),
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
        $queue ??= $this->jobQueueConfig->getDefaultQueue();

        $this->instantiateJobQueue($queue)->publishJob(
            job: new Job(
                payload: $payload,
            ),
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
        $queue ??= $this->jobQueueConfig->getDefaultQueue();

        $this->instantiateJobQueue($queue)->publishJob(
            job: new Job(
                payload: $this->createPayload($job, $queue, $data),
            ),
            availableAt: $this->availableAt($delay)
        );
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     */
    public function pop($queue = null): ?IlluminateJob
    {
        return null;
    }

    private function instantiateJobQueue(string $queue): JobQueue
    {
        return new JobQueue(
            name: $this->jobQueueConfig->getJobQueueName($queue),
            workerUrl: $this->jobQueueConfig->getJobQueueWorkerUrl($queue),
        );
    }
}
