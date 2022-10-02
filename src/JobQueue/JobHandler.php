<?php

namespace Shipmate\Shipmate\JobQueue;

use Illuminate\Container\Container;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Shipmate\Shipmate\JobQueue\Google\GoogleClient;

class JobHandler
{
    public static function new(): static
    {
        return app(static::class);
    }

    public function handle(JobPayload $jobPayload, string $jobName, int $jobExecutionCount): void
    {
        $connectionName = $jobPayload->getConnectionName() ?? config('queue.default');

        $config = JobQueueConfig::readFromConnection($connectionName);
        $googleClient = GoogleClient::new($config);

        $queueName = $jobPayload->getQueueName() ?? $config->getQueueName();
        $maxAttempts = $this->getMaxAttempts($googleClient, $queueName);
        $retryUntil = $this->getRetryUntil($googleClient, $queueName, $jobName, $jobExecutionCount);

        $job = new Job(
            container: Container::getInstance(),
            googleClient: $googleClient,
            jobPayload: $jobPayload,
            jobName: $jobName,
            attempts: $jobExecutionCount,
            maxTries: $maxAttempts,
            retryUntil: $retryUntil,
            connectionName: $connectionName,
            queue: $queueName,
        );

        $this->instantiateWorker()->process(
            connectionName: $connectionName,
            job: $job,
            options: new WorkerOptions,
        );
    }

    private function getMaxAttempts(GoogleClient $client, string $queueName): int
    {
        $retryConfig = $client->getQueue($queueName)->getRetryConfig();

        $maxAttempts = $retryConfig->getMaxAttempts();

        return $maxAttempts === -1 ? 0 : $maxAttempts;
    }

    /*
     * If the job is being attempted again we also check if a max retry duration has been set. If that duration has
     * passed, it should stop trying altogether.
     */
    private function getRetryUntil(GoogleClient $client, string $queueName, string $jobName, int $attempt): ?int
    {
        if ($attempt === 0) {
            return null;
        }

        return $client->getJob($queueName, $jobName)->getRetryUntilTimestamp();
    }

    private function instantiateWorker(): Worker
    {
        return app('queue.worker');
    }
}
