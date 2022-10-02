<?php

namespace Shipmate\Shipmate\JobQueue\Google;

use Google\Cloud\Tasks\V2\CloudTasksClient;
use Google\Cloud\Tasks\V2\HttpMethod;
use Google\Cloud\Tasks\V2\HttpRequest;
use Google\Cloud\Tasks\V2\OidcToken;
use Google\Cloud\Tasks\V2\Task;
use Google\Protobuf\Timestamp;
use Shipmate\Shipmate\JobQueue\JobQueueConfig;

class GoogleClient
{
    private CloudTasksClient $client;

    public function __construct(
        private JobQueueConfig $config,
    ) {
        $this->client = new CloudTasksClient([
            'projectId' => $this->config->getProjectId(),
            'keyFile' => $this->config->getKey(),
        ]);
    }

    public static function new(JobQueueConfig $config): static
    {
        return app(static::class, compact('config'));
    }

    // Queues

    public function getQueue(string $queueName): GoogleQueue
    {
        $fullyQualifiedQueueName = $this->generateQueueName($queueName);

        $queue = $this->client->getQueue($fullyQualifiedQueueName);

        return new GoogleQueue($queue);
    }

    // Jobs

    public function getJob(string $queueName, string $jobName): GoogleJob
    {
        $fullyQualifiedTaskName = $this->generateTaskName($queueName, $jobName);

        $task = $this->client->getTask($fullyQualifiedTaskName);

        return new GoogleJob($this, $queueName, $task);
    }

    public function createJob(string $queueName, string $payload, int $availableAt): GoogleJob
    {
        $httpRequest = $this->instantiateHttpRequest();
        $httpRequest->setUrl($this->config->getWorkerUrl());
        $httpRequest->setHttpMethod(HttpMethod::POST);
        $httpRequest->setBody($payload);

        $token = new OidcToken;
        $token->setServiceAccountEmail($this->config->getEmail());
        $httpRequest->setOidcToken($token);

        $task = $this->instantiateTask();
        $task->setHttpRequest($httpRequest);

        if ($availableAt > time()) {
            $task->setScheduleTime(new Timestamp(['seconds' => $availableAt]));
        }

        $fullyQualifiedQueueName = $this->generateQueueName($queueName);

        $task = $this->client->createTask($fullyQualifiedQueueName, $task);

        return new GoogleJob($this, $queueName, $task);
    }

    public function deleteJob(string $queueName, string $jobName): void
    {
        $fullyQualifiedTaskName = $this->generateTaskName($queueName, $jobName);

        $this->client->deleteTask($fullyQualifiedTaskName);
    }

    // Helpers

    private function generateQueueName(string $queueName): string
    {
        return $this->client->queueName(
            project: $this->config->getProjectId(),
            location: $this->config->getRegionId(),
            queue: $queueName,
        );
    }

    private function generateTaskName(string $queueName, string $taskName): string
    {
        return $this->client->taskName(
            project: $this->config->getProjectId(),
            location: $this->config->getRegionId(),
            queue: $queueName,
            task: $taskName
        );
    }

    private function instantiateHttpRequest(): HttpRequest
    {
        return app(HttpRequest::class);
    }

    private function instantiateTask(): Task
    {
        return app(Task::class);
    }
}
