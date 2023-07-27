<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Google\Protobuf\Duration;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;

class JobHandler
{
    public static function new(): static
    {
        return app(static::class);
    }

    public function handle(Job $job, string $bearerToken): void
    {
        $connection = $job->getConnection() ?? config('queue.default');

        $jobQueueConfig = JobQueueConfig::readFromConnection($connection);

        $queue = $job->getQueue() ?? $jobQueueConfig->getDefaultQueue();

        $jobQueue = new JobQueue(
            name: $jobQueueConfig->getJobQueueName($queue),
            workerUrl: $jobQueueConfig->getJobQueueWorkerUrl($queue),
        );

        $jobQueue->authenticateRequest($bearerToken);

        $retryUntil = $this->getRetryUntil($jobQueue, $job);

        $queueJob = new QueueJob(
            jobQueue: $jobQueue,
            job: $job,
            maxTries: $jobQueue->getMaxTries(),
            retryUntil: $retryUntil,
            connectionName: $connection,
            queue: $queue,
        );

        $this->instantiateWorker()->process(
            connectionName: $connection,
            job: $queueJob,
            options: new WorkerOptions,
        );
    }

    /*
     * If the job is being attempted again we also check if a max retry duration has been set. If that duration has
     * passed, it should stop trying altogether.
     */
    private function getRetryUntil(JobQueue $jobQueue, Job $job): ?int
    {
        if ($job->getAttempts() === 0) {
            return null;
        }

        return $jobQueue->getRetryDeadlineOfJob($job->getName());
    }

    private function instantiateWorker(): Worker
    {
        return app('queue.worker');
    }
}
