<?php

namespace Shipmate\LaravelShipmate\JobQueue\Google;

use Google\Cloud\Tasks\V2\CloudTasksClient;
use Google\Cloud\Tasks\V2\HttpMethod;
use Google\Cloud\Tasks\V2\HttpRequest;
use Google\Cloud\Tasks\V2\OidcToken;
use Google\Cloud\Tasks\V2\Task;
use Google\Protobuf\Timestamp;
use Shipmate\LaravelShipmate\JobQueue\JobQueueConfig;
use Shipmate\LaravelShipmate\ShipmateConfig;

class GoogleClient
{
    private ShipmateConfig $shipmateConfig;

    private CloudTasksClient $client;

    public function __construct(
        private JobQueueConfig $jobQueueConfig,
    ) {
        $this->shipmateConfig = ShipmateConfig::new();

        $this->client = new CloudTasksClient([
            'projectId' => $this->shipmateConfig->getProjectId(),
            'keyFile' => $this->shipmateConfig->getKey(),
        ]);
    }

    public static function new(JobQueueConfig $jobQueueConfig): static
    {
        return app(static::class, compact('jobQueueConfig'));
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
        $httpRequest->setUrl($this->jobQueueConfig->getWorkerUrl());
        $httpRequest->setHttpMethod(HttpMethod::POST);
        $httpRequest->setBody($payload);

        $token = new OidcToken;
        $token->setServiceAccountEmail($this->shipmateConfig->getEmail());
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
            project: $this->shipmateConfig->getProjectId(),
            location: $this->shipmateConfig->getRegionId(),
            queue: $queueName,
        );
    }

    private function generateTaskName(string $queueName, string $taskName): string
    {
        return $this->client->taskName(
            project: $this->shipmateConfig->getProjectId(),
            location: $this->shipmateConfig->getRegionId(),
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
